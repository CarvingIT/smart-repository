<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class SystemInfoService
{
    /**
     * Get all system information
     */
    public function getAllInfo()
    {
        return [
            'permissions' => $this->checkPermissions(),
            'dependencies' => $this->checkMissingDependencies(),
            'elasticsearch' => $this->checkElasticsearch(),
            'ocr' => $this->checkOcrLibraries(),
            'versions' => $this->getVersionInfo(),
            "database" => $this->getDatabaseInfo(),
            "disk_space" => $this->getStorageInfo(),
        ];
    }

    /**
     * 1. Check for permission issues
     */
    public function checkPermissions() {
        $directories = [
            "storage" => storage_path(),
            "storage/app" => storage_path('app'),
            "storage/logs" => storage_path('logs'),
            "storage/framework/cache" => storage_path('framework/cache'),
            "bootstrap/cache" => base_path('bootstrap/cache'),
        ];

        $results = [];

        foreach ($directories as $name => $path) {
            $results[$name] = [
                'path' => $path,
                "exists" => file_exists($path) && is_dir($path),
                "readable" => is_readable($path),
                'writable' => is_writable($path),
            ];
        }

        return $results;
    }

    /**
     * 2. Check for missing dependencies
     */
    public function checkMissingDependencies() { 
        $commands = [
            "pdftotext" => [
                "description" => "PDF text extractor",
                "required" => true,
                "package" => "spatie/pdf-to-text"
            ],
        ];

        $results = [];

        foreach ($commands as $cmd => $info){
            exec("which $cmd 2>&1", $output, $return_code);
            $installed = ($return_code === 0);

            $results[$cmd] = [
                "description" => $info['description'],
                "installed" => $installed,
                "required" => $info['required'],
                "package" => $info['package'],
            ];
        }

        return $results;
    }

    /**
     * 3. Check if Elasticsearch is running
     */
    private function createElasticsearchClient() {
        $elastic_hosts = env("ELASTIC_SEARCH_HOSTS", "localhost:9200");
        $hosts = explode(",", $elastic_hosts);

        $client_builder = ClientBuilder::create()->setHosts($hosts);

        $password = env("ELASTIC_PASSWORD", "");

        if (!empty($password)){
            $client_builder->setBasicAuthentication("elastic", $password);
        }

        $ca_bundle = "/etc/elasticsearch/certs/http_ca.crt";
        if (file_exists($ca_bundle)){
            $client_builder->setCABundle($ca_bundle);
        }

        return $client_builder->build();
    }

    private function parseIndices($indices_response){
        $indices = [];

        if(is_array($indices_response)){
            foreach ($indices_response as $line){
                if(isset($line['index'])){
                    $indices[] = $line['index'];
                }
            }
        }

        return $indices;
    }

    public function checkElasticsearch() { 
        $configured = !empty(env("ELASTIC_SEARCH_HOSTS", ""));

        if (!$configured) {
            return [
                'configured' => false,
                'running' => false,
                'error' => 'Elasticsearch not configured in .env',
            ];
        }

        try {
            $client = $this->createElasticsearchClient();

            $info = $client->info();

            $health = $client->cluster()->health();

            $indices = $client->cat()->indices();

            return [
                'configured' => true,
                'running' => true,
                'reachable' => true,
                'version' => $info['version']['number'] ?? 'unknown',
                'cluster_name' => $info['cluster_name'] ?? 'unknown',
                'cluster_health' => $health['status'] ?? 'unknown',
                "indices" => $this->parseIndices($indices),
                'error' => null,
            ];
        } catch (Exception $e) {
            return [
                'configured' => true,
                'running' => false,
                'reachable' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * 4. Check OCR libraries for different languages
     */
    public function checkOcrLibraries() { 
        $results = [];

        exec("which tesseract 2>&1", $output, $return_code);
        $teseract_installed = ($return_code === 0);

        $results["tesseract"] = [
            "installed" => $teseract_installed,
            "version" => null,
            "path" => null,
            "languages" => [],
            "language_count" => 0,
        ];


        if ($teseract_installed){

            $results["tesseract"]["path"] = trim(implode(" ", $output));
            exec("tesseract --version 2>&1", $version_output);
            $results["tesseract"]["version"] = trim($version_output[0] ?? 'unknown');

            exec("tesseract --list-langs 2>&1", $lang_output);
            $results["tesseract"]["languages"] = array_slice($lang_output, 1);
            $results["tesseract"]["language_count"] = count($results["tesseract"]["languages"]);
        }

        exec("which ocrmypdf 2>&1", $ocr_output, $ocr_code);
        $results['ocrmypdf'] = [
            'installed' => ($ocr_code === 0),
            'path' => ($ocr_code === 0) ? trim(implode('', $ocr_output)) : null,
        ];

        return $results;

    }

    /**
     * 5. Get version of dependencies
     */
    
    private function getComposerPackageVersions() {
        $packages = [];
        $composer_lock_path = base_path('composer.lock');

        if (!file_exists($composer_lock_path)) {
            return ["error" => "composer.lock file not found"];
        }

        $composer_lock = json_decode(file_get_contents($composer_lock_path), true);

        if(!$composer_lock || !isset($composer_lock['packages'])) {
            return ["error" => "Invalid composer.lock file"];
        }

        $important_packages = [
            'laravel/framework',
            'elasticsearch/elasticsearch',
            'spatie/pdf-to-text',
            'thiagoalessio/tesseract_ocr',
            'mishagp/ocrmypdf',
            'spatie/crawler',
        ];

        foreach ($composer_lock['packages'] as $package) {
            $package_name = $package['name'] ?? null;

            if($package_name && in_array($package_name, $important_packages)) {
                $packages[$package_name] = [
                    'version' => $package['version'] ?? 'unknown',
                    'name' => $package_name,
                ];
            }
        }

        return $packages;
    }


    public function getVersionInfo() { 
        $results = [];

        $results["php"] = [
            "version" => PHP_VERSION,
            "name" => "PHP",
        ];

        $results["laravel"] = [
            "version" => app()->version(),
            "name" => "Laravel",
        ];


        $results["packages"] = $this->getComposerPackageVersions();

        return $results;
    }

    // Database Information

    private function getDatabaseVersion($connection){
        try {
            switch ($connection) {
                case "mysql":
                case "pgsql":
                    $result = DB::select("SELECT VERSION() as version");
                    return $result[0]->version ?? 'unknown';

                case "sqlite":
                    $result = DB::select("SELECT sqlite_version() as version");
                    return $result[0]->version ?? 'unknown';

                case "sqlsrv":
                    $result = DB::select("SELECT @@VERSION as version");
                    return $result[0]->version ?? 'unknown';

                default:
                    return 'unknown';
            }
        } catch (Exception $e) {
            return "Unable to determine";
        }
    }

    public function getDatabaseInfo(){
        try {
            $connection = config("database.default");
            $config = config("database.connections.{$connection}");

            if(empty($config)){
                return [
                    "configured" => false,
                    "connected" => false,
                    "message" => "No database configured",
                ];
            }

            $pdo = DB::connection()->getPdo();
            $version = $this->getDatabaseVersion($connection);

            $info = [
                "configured" => true,
                "connected" => true,
                "connection_type" => $connection,
                "driver" => $config['driver'] ?? 'unknown',
                "version" => $version,
                "error" => null,
            ];

            if ($connection === "sqlite") {
                $info["database"] = $config["database"] ?? 'N/A';
            } else {
                $info["host"] = $config["host"] ?? "N/A";
                $info["port"] = $config["port"] ?? "N/A";
                $info["database"] = $config["database"] ?? 'N/A';
                $info["username"] = $config["username"] ?? 'N/A';
            }

            return $info;

        } catch (Exception $e) {
            return [
               "configured" => !empty($config),
               "connected" => false,
               "connection_type" => $connection ?? 'unknown',
               "error" => $e->getMessage(),
            ];
        }
    }

    // Storage

    private function formatBytes($bytes, $precision = 2){
        if ($bytes == 0) return "0 B";
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$precision}f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
    }

    private function getDiskStatus($percentage) {
        if ($percentage >= 95){
            return "critical";
        } elseif ($percentage >= 75) {
            return "warning";
        } else {
            return "ok";
        }
    }

    public function getStorageInfo() {
        $paths = [
            "storage" => storage_path(),
            "application" => base_path(),
            "public" => public_path(),
        ];

        $results = [];

        foreach($paths as $name => $path) {
            if(file_exists($path)) {
                $total = disk_total_space($path);
                $free = disk_free_space($path);
                $used = $total - $free;
                $percentage = ($total > 0) ? round(($used / $total) * 100, 2) : 0;

                $results[$name] = [
                    "path" => $path,
                    "total" => $total,
                    "used" => $used,
                    "free" => $free,
                    "percentage" => $percentage,
                    "total_human" => $this->formatBytes($total),
                    "used_human" => $this->formatBytes($used),
                    "free_human" => $this->formatBytes($free),
                    "status" => $this->getDiskStatus($percentage),
                ];
            }
        }

        return $results;
    }

}