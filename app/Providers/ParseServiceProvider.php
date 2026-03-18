<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Parse\ParseClient;

class ParseServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Initialize Parse Client with credentials from .env file
        ParseClient::initialize(
            env('PARSE_APP_ID'), 
            env('PARSE_REST_API_KEY'),
            env('PARSE_MASTER_KEY')
        );
        ParseClient::setServerURL(env('PARSE_SERVER_URL'), '/parse');
    }

    public function boot()
    {
        //
    }
}
