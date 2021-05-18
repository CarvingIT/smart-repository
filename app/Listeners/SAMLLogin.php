<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\User;
use Illuminate\Support\Facades\Auth;

class SAMLLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
		Log::info('Someone logged in');
	    $messageId = $event->getSaml2Auth()->getLastMessageId();
		// Add your own code preventing reuse of a $messageId to stop replay attacks

		Log::info('Message ID: '.$messageId);
		$user = $event->getSaml2User();
		$userData = [
        'id' => $user->getUserId(),
        'attributes' => $user->getAttributes(),
        'assertion' => $user->getRawSamlAssertion()
    	];

		//Log::info('User info: '.json_encode($userData));
		Log::info('Username: '.$userData['attributes']['username'][0]);
        $laravelUser = User::where('email', $userData['attributes']['username'][0])->first();//find user by ID or attribute
        //if it does not exist create it and go on  or show an error message
		if($laravelUser){
        	Auth::login($laravelUser);
			Log::info('ID: '.$laravelUser->id);
		}
		else{
			// create and login
			Log::info('User does not exist. Need to create.');
			$laravelUser = new User;
			$laravelUser->email = $userData['attributes']['username'][0];
			$laravelUser->name = $userData['attributes']['name'][0];
			$laravelUser->password = '! Created through SSO';
			$laravelUser->save();
        	Auth::login($laravelUser);
		}
    }
}
