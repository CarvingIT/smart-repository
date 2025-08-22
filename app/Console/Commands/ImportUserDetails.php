<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use App\User;

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
        $user_details = file($filename);
        $headings = explode(",", ltrim(rtrim(array_shift($user_details))));
        $users = [];
        foreach($user_details as $u){
            $details = explode(',', $u);
            $user = [];
            for($i=0; $i<count($headings); $i++){
               $user[$headings[$i]] = $details[$i]; 
            } 
            $users[] = $user;
        }
        $known_columns = ['name', 'email', 'organization'];
        foreach($users as $user){
            $u = new User;
            foreach($known_columns as $kc){
               if(!empty($user[$kc])){
                $u->{$kc} = $user[$kc]; 
               }
            }
            foreach(array_keys($user) as $att){
                if(in_array($att, $known_columns)) continue;
                $extra_attributes[$att] = $user[$att];
            }
            $u->password = '!';
            $u->extra_attributes = json_encode($extra_attributes);
            try{
                $u->save();
            }
            catch(\Exception $e){
                echo 'New user not created. Error: '.$e->getMessage()."\n";
            }
        }
        return Command::SUCCESS;
    }
}
