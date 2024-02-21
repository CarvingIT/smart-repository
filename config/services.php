<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],
	// oauth 
    'google' => [
    'client_id'     => env('GL_ID'),
    'client_secret' => env('GL_SECRET'),
    'redirect'      => env('APP_URL') . '/oauth/google/callback',
    ],
    'facebook' => [
    'client_id'     => env('FB_ID'),
    'client_secret' => env('FB_SECRET'),
    'redirect'      => env('APP_URL') . '/oauth/facebook/callback',
    ],
	// more are supported
	// facebook, twitter, linkedin, google, github, gitlab, bitbucket

	'paytm-wallet' => ['env' => env('PAYTM_ENVIRONMENT'),
		'merchant_id' => env('PAYTM_MERCHANT_ID'),
		'merchant_key' => env('PAYTM_MERCHANT_KEY'),
		'merchant_website' => env('PAYTM_MERCHANT_WEBSITE'),
		'channel' => env('PAYTM_CHANNEL'),
		'industry_type' => env('PAYTM_INDUSTRY_TYPE'),
	],

];
