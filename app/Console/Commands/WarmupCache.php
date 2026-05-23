<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\WardManagementController;

class WarmupCache extends Command
{
    protected $signature = 'cache:warmup';
    protected $description = 'Warm up the cache for faster loading';

    public function handle()
    {
        $this->info('Warming up cache...');
        
        try {
            app()->make(WardManagementController::class)->index();
            $this->info('✓ Cache warmed up successfully!');
        } catch (\Exception $e) {
            $this->error('✗ Failed to warm up cache: ' . $e->getMessage());
        }
        
        return Command::SUCCESS;
    }
}