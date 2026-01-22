<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizeProduction extends Command
{
    protected $signature = 'app:optimize-production';
    protected $description = 'Optimize application for production environment';

    public function handle(): int
    {
        $this->info('🔧 Optimizing application for production...');

        try {
            $this->info('Clearing all caches...');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');

            $this->info('📦 Caching configuration...');
            Artisan::call('config:cache');

            $this->info('🛣️ Caching routes...');
            Artisan::call('route:cache');

            $this->info('👁️ Caching views...');
            Artisan::call('view:cache');

            $this->info('🎯 Running migrations...');
            Artisan::call('migrate', ['--force' => true]);

            $this->info('✅ Production optimization completed successfully!');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Optimization failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
