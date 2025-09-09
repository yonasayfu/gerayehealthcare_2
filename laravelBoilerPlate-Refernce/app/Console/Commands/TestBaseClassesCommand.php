<?php

namespace App\Console\Commands;

use App\DTOs\TestDTO;
use App\Services\CachedDropdownService;
use App\Services\TestService;
use Illuminate\Console\Command;

class TestBaseClassesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:base-classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the base classes functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing base classes functionality...');

        // Test DTO
        $this->info('Testing DTO...');
        $dto = TestDTO::create([
            'id' => 1,
            'name' => 'Test Item',
            'description' => 'This is a test item',
            'tags' => ['test', 'example'],
        ]);

        $this->info('DTO created successfully:');
        $this->line('ID: ' . $dto->id);
        $this->line('Name: ' . $dto->name);
        $this->line('Description: ' . $dto->description);
        $this->line('Tags: ' . json_encode($dto->tags));

        // Test Service
        $this->info('Testing Service...');
        $service = new TestService();
        $request = new \Illuminate\Http\Request();
        $request->merge(['search' => 'Test Item 1']);

        $data = $service->getAllTestData($request);
        $this->info('Service search returned ' . count($data) . ' items');

        // Test CachedDropdownService
        $this->info('Testing CachedDropdownService...');
        $users = CachedDropdownService::getUsers();
        $this->info('CachedDropdownService returned ' . count($users) . ' users');

        $this->info('All tests completed successfully!');

        return 0;
    }
}
