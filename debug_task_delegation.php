<?php
// Debug script to check task delegation data structure
require_once 'vendor/autoload.php';

use App\Models\TaskDelegation;

// Get a task delegation record
$task = TaskDelegation::first();

if ($task) {
    echo "Task Delegation Data:\n";
    echo "ID: " . $task->id . "\n";
    echo "Title: " . $task->title . "\n";
    echo "Due Date: " . $task->due_date . "\n";
    echo "Due Date Type: " . gettype($task->due_date) . "\n";
    echo "Due Date Class: " . (is_object($task->due_date) ? get_class($task->due_date) : 'Not an object') . "\n";

    // Check if it's a Carbon instance
    if ($task->due_date instanceof \Carbon\Carbon) {
        echo "Due Date Formatted: " . $task->due_date->format('Y-m-d') . "\n";
    }

    // Convert to array to see all data
    echo "\nAs Array:\n";
    print_r($task->toArray());
} else {
    echo "No task delegation found\n";
}
