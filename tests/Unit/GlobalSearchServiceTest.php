<?php

namespace Tests\Unit;

use App\Services\GlobalSearchService;
use Tests\TestCase;

class GlobalSearchServiceTest extends TestCase
{
    /** @test */
    public function it_returns_empty_array_for_short_queries()
    {
        $service = new GlobalSearchService();
        $results = $service->search('a');

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    /** @test */
    public function it_returns_empty_array_for_empty_queries()
    {
        $service = new GlobalSearchService();
        $results = $service->search('');

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }
}
