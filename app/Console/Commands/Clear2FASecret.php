<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Back4AppService;

class Clear2FASecret extends Command
{
    protected $signature = 'clear:2fa-secret {userId}';
    protected $description = 'Clear 2FA secret for a specific user';

    public function handle()
    {
        $userId = $this->argument('userId');
        $back4AppService = app(Back4AppService::class);
        
        $this->info("Starting to clear 2FA secret for user: $userId");
        
        try {
            // Clear the 2FA secret
            $result = $back4AppService->updateUser($userId, [
                'google2fa_secret' => null
            ]);
            
            $this->info("2FA secret cleared successfully for user: $userId");
            $this->info("Result: " . json_encode($result));
            
        } catch (\Exception $e) {
            $this->error("Error clearing 2FA secret: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
