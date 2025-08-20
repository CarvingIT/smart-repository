<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Users;

class ImportUserDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:ImportUserDetails {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Users Details in Users Table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = $this->argument('filename'); 
        $headings = $u_details = [];
        $user_details = file($filename);

        $line_count = 0;
        foreach($user_details as $u){
            $u_d = explode(",",$u);
            print_r($u_d); echo "\n";
            if($line_count == 0){
                $headings = $u_d;                
            }             
            else{
                $u_t = \App\User::where('email',$u_d[2])->first();
                if(!empty($u_t->email) && $u_t->email == $u_d[2]){ continue; }
                $u_t = new \App\User;
                $u_t->name = $u_d[1];
                $u_t->organization = 'Swadhaa Waldorf School';
                $u_t->email = $u_d[2];
                $u_t->password = $this->randomPassword();
                $u_t->occupation = $u_d[3];
                $extra_attributes['Area'] = $u_d[0];
                $extra_attributes['Class Email ID'] = $u_d[4];
                $extra_attributes['Contact No'] = $u_d[5];
                $extra_attributes['Personal email id'] = $u_d[6];
                $u_t->extra_attributes = json_encode($extra_attributes);
                $u_t->save();
            }
        $line_count++;
        }


        return Command::SUCCESS;
    }

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

////
}
