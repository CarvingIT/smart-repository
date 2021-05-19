<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
	'App\Events\DocumentSaved'=>[
		'App\Listeners\DocumentSaved',
	],
	'App\Events\DocumentDeleted'=>[
		'App\Listeners\DocumentDeleted',
	],

	'Aacotroneo\Saml2\Events\Saml2LoginEvent'=>[
		'App\Listeners\SAMLLogin',
	],
	'Aacotroneo\Saml2\Events\Saml2LogoutEvent'=>[
		'App\Listeners\SAMLLogout',
	],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
