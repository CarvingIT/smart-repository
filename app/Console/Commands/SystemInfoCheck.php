<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SystemInfoService;

class SystemInfoCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:check {--section=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check system information and health';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(){
        $service = new SystemInfoService(); 
        $section = $this->option('section');
        
        if ($section === 'all' || $section === 'permissions') {
            $this->info('=== Checking Permissions ===');
            $permissions = $service->checkPermissions();
            
            foreach ($permissions as $name => $details) {
                $this->line("\n📁 {$name}");
                $this->line("   Path: {$details['path']}");
                $this->line("   Exists: " . ($details['exists'] ? '✓ Yes' : '✗ No'));
                $this->line("   Readable: " . ($details['readable'] ? '✓ Yes' : '✗ No'));
                $this->line("   Writable: " . ($details['writable'] ? '✓ Yes' : '✗ No'));
                
                if ($details['exists'] && $details['readable'] && $details['writable']) {
                    $this->info("   Status: ✓ OK");
                } else {
                    $this->error("   Status: ✗ ISSUE DETECTED");
                }
            }
        }

        if ($section === 'all' || $section === 'dependencies') {
            $this->info("\n=== Checking System Dependencies ===");
            $dependencies = $service->checkMissingDependencies();
            
            foreach ($dependencies as $cmd => $details) {
                $this->line("\n🔧 {$cmd}");
                $this->line("   Description: {$details['description']}");
                $this->line("   Package: {$details['package']}");
                $this->line("   Required: " . ($details['required'] ? 'Yes' : 'No (Optional)'));
                $this->line("   Installed: " . ($details['installed'] ? '✓ Yes' : '✗ No'));
                
                if ($details['installed']) {
                    $this->info("   Status: ✓ OK");
                } else {
                    if ($details['required']) {
                        $this->error("   Status: ✗ MISSING (Required)");
                    } else {
                        $this->warn("   Status: ⚠ Not installed (Optional)");
                    }
                }
            }
        }

        if ($section === "all" || $section === "elasticsearch"){
            $this->info("\n=== Checking Elasticsearch ===");
            $es = $service->checkElasticsearch();
            
            $this->line("\n🔍 Elasticsearch Status");
            $this->line("   Configured: " . ($es['configured'] ? '✓ Yes' : '✗ No'));
            
            if ($es['configured']) {
                $this->line("   Running: " . ($es['running'] ? '✓ Yes' : '✗ No'));
                
                if ($es['running']) {
                    $this->line("   Version: {$es['version']}");
                    $this->line("   Cluster Name: {$es['cluster_name']}");
                    $this->line("   Cluster Health: {$es['cluster_health']}");
                    
                    if (!empty($es['indices'])) {
                        $this->line("   Indices: " . implode(', ', $es['indices']));
                    }
                    
                    // Status based on health
                    if ($es['cluster_health'] === 'green') {
                        $this->info("   Status: ✓ HEALTHY");
                    } elseif ($es['cluster_health'] === 'yellow') {
                        $this->warn("   Status: ⚠ WARNING");
                    } else {
                        $this->error("   Status: ✗ UNHEALTHY");
                    }
                } else {
                    $this->error("   Status: ✗ NOT REACHABLE");
                    $this->line("   Error: {$es['error']}");
                }
            } else {
                $this->warn("   Status: ⚠ Not configured");
            }
        }
        
        if ($section === 'all' || $section === 'ocr') {
            $this->info("\n=== Checking OCR Libraries ===");
            $ocr = $service->checkOcrLibraries();
            
            // Tesseract
            $this->line("\n🔤 Tesseract OCR");
            $this->line("   Installed: " . ($ocr['tesseract']['installed'] ? '✓ Yes' : '✗ No'));
            
            if ($ocr['tesseract']['installed']) {
                $this->line("   Version: {$ocr['tesseract']['version']}");
                $this->line("   Path: {$ocr['tesseract']['path']}");
                $this->line("   Languages Available: {$ocr['tesseract']['language_count']}");
                
                if (!empty($ocr['tesseract']['languages'])) {
                    $this->line("   Language List: " . implode(', ', $ocr['tesseract']['languages']));
                }
                
                $this->info("   Status: ✓ OK");
            } else {
                $this->error("   Status: ✗ NOT INSTALLED");
            }
            
            // ocrmypdf
            $this->line("\n📄 ocrmypdf");
            $this->line("   Installed: " . ($ocr['ocrmypdf']['installed'] ? '✓ Yes' : '✗ No'));
            
            if ($ocr['ocrmypdf']['installed']) {
                $this->line("   Path: {$ocr['ocrmypdf']['path']}");
                $this->info("   Status: ✓ OK");
            } else {
                $this->warn("   Status: ⚠ Not installed");
            }
        }

        if ($section === 'all' || $section === 'versions') {
            $this->info("\n=== Version Information ===");
            $versions = $service->getVersionInfo();
            
            $this->line("\n💻 Core Versions");
            $this->line("   PHP: {$versions['php']['version']}");
            $this->line("   Laravel: {$versions['laravel']['version']}");
            
            if (!empty($versions['packages']) && !isset($versions['packages']['error'])) {
                $this->line("\n📦 Key Packages");

                foreach ($versions['packages'] as $package_name => $details) {
                    $this->line("   {$package_name}: {$details['version']}");
                }
            } else if (isset($versions['packages']['error'])) {
                $this->warn("   Could not read package versions: {$versions['packages']['error']}");
            }
        }
        
        if ($section === 'all' || $section === 'database') {
            $this->info("\n=== Database Information ===");
            $db = $service->getDatabaseInfo();
            
            if (!$db['configured']) {
                $this->warn("\n⚠ {$db['message']}");
            } else {
                $this->line("\n💾 Database Configuration");
                $this->line("   Connection Type: {$db['connection_type']}");
                
                if ($db['connection_type'] === 'sqlite') {
                    $this->line("   Type: File-based database");
                    $this->line("   Database File: {$db['database']}");
                } else {
                    $this->line("   Host: {$db['host']}");
                    $this->line("   Port: {$db['port']}");
                    $this->line("   Database: {$db['database']}");
                    $this->line("   Username: {$db['username']}");
                }
                
                if ($db['connected']) {
                    $this->line("   Version: {$db['version']}");
                    $this->info("   Status: ✓ Connected & Working");
                } else {
                    $this->error("   Status: ✗ Cannot Connect");
                    $this->line("   Error: {$db['error']}");
                }
            }
        }
        
        return Command::SUCCESS;
    }
}
