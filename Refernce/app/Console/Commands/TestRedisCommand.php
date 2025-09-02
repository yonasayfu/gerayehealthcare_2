<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TestRedisCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:redis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Redis connectivity and caching';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Redis connectivity...');

        // Test storing data in Redis cache
        $testKey = 'redis_test_key';
        $testValue = 'Hello from Redis! This is a test value.';

        $this->info("Storing value in Redis cache with key: {$testKey}");
        Cache::put($testKey, $testValue, 300); // Store for 5 minutes

        // Test retrieving data from Redis cache
        $this->info('Retrieving value from Redis cache...');
        $retrievedValue = Cache::get($testKey);

        if ($retrievedValue) {
            $this->info("Success! Retrieved value: {$retrievedValue}");
        } else {
            $this->error('Failed to retrieve value from Redis cache');

            return 1;
        }

        // Test cache deletion
        $this->info('Testing cache deletion...');
        Cache::forget($testKey);

        // Verify deletion
        $deletedValue = Cache::get($testKey);
        if ($deletedValue === null) {
            $this->info('Success! Cache entry deleted successfully.');
        } else {
            $this->error('Failed to delete cache entry');

            return 1;
        }

        $this->info('All Redis tests passed successfully!');

        return 0;
    }
}
