<?php

use App\Services\Back4AppService;

Route::get('/test-back4app', function () {
    $service = new Back4AppService();
    
    // Test with some dummy credentials to see the API response
    $result = $service->login('test@example.com', 'testpassword');
    
    return response()->json([
        'service_config' => [
            'api_url' => config('app.BACK4APP_API_URL'),
            'app_id' => config('app.BACK4APP_APP_ID'),
            'has_master_key' => !empty(config('app.BACK4APP_MASTER_KEY')),
            'has_rest_api_key' => !empty(env('PARSE_REST_API_KEY'))
        ],
        'login_result' => $result
    ]);
});
