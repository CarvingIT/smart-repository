<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
		/*
		config(['filesystems.disks'=>[
		    'mysftp'=>[
            'driver' => 'sftp',
            'host' => '162.241.149.43',
            'port' => 22,
            'username' => 'shraddha',
            'password' => 'Shraddha123!',
            'privateKey' => '',
            'root' => '/home/shraddha/CITPL_SR',
            'timeout' => 100,
    		]
		]
		]);
		*/
    }
}
