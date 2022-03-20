<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Disk;

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
		try{
			$config_disks = config('filesystems.disks');	
			// get disks defined in the database
			$db_disks = []; 
			foreach(Disk::all() as $d){
				$db_disks[ $d->name ] = json_decode($d->config, true);
			}
			$all_disks = array_merge($config_disks, $db_disks);
			// update the filesystem config with all disks
			config(['filesystems.disks'=>$all_disks]);
		}
		catch(\Exception $e){
			// do nothing
			// this try-catch is required for test environment where db is re-created on every run
		}
    }
}
