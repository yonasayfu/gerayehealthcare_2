<?php

use App\Mcp\Servers\LaravelMcp;
use Laravel\Mcp\Server\Facades\Mcp;

// Register the Laravel Boost server (built-in)
// This is automatically available through the boost:mcp command

// Register our custom LaravelMcp server
Mcp::local('laravel-mcp', LaravelMcp::class); // Start with ./artisan mcp:start laravel-mcp
