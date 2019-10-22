<?php
namespace App\Helpers;
use Config;
use DB;

class DatabaseConnection
{
    public static function setConnection($con_ar)
    {
        echo $con_ar['database'];
        config(['database.connections.onthefly' => [
            'driver' => 'mysql',
            'host' => $con_ar['host'],
            'username' => $con_ar['username'],
            'password' => $con_ar['password'],
            'database'=>$con_ar['database']
        ]]);

        DB::connection('onthefly');
    }
}
