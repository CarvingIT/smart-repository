<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\UserRole;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:MakeAdmin {email_address : Email address of the administrator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new administrator.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$email_address = $this->argument('email_address');
		echo "Creating a new administrator with email address - $email_address\n";
		// check if the email already exists
		$user = User::where('email', $email_address)->first();
		if(!$user){
			$user = new User;
		}
		$user->email = $email_address;
		$password = Str::uuid()->toString();
		$user->password = Hash::make($password);
		$user->name = "Administrator";
		$user->save();
		$user_role = new UserRole;
		$user_role->user_id = $user->id;
		$user_role->role_id = 1;
		$user_role->save();
		echo "Password: ".$password."\n";
		echo "You can change the password after logging in to the application.\n";
    }
}
