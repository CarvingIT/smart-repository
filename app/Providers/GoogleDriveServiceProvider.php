<?php

namespace App\Providers;

use Google_Client;
//use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use Masbug\Flysystem\GoogleDrive\GoogleDriveAdapter;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Storage;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('google', function($app, $config) {
            $client = new Google_Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);
            $service = new \Google_Service_Drive($client);

            $options = [];
            if(isset($config['teamDriveId'])) {
                $options['teamDriveId'] = $config['teamDriveId'];
            }

            //$adapter = new GoogleDriveAdapter($service, $config['folderId'], $options);
            $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folderId']);

            //return new Filesystem($adapter);
            return new FilesystemAdapter(
        new \League\Flysystem\Filesystem($adapter),
        $adapter,
        $config
        );
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

