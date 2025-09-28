<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InventoryAlert;

$alert = InventoryAlert::with('delegatedTask.assignee')->whereNotNull('delegated_task_id')->first();

if ($alert) {
    echo "Alert ID: " . $alert->id . "\n";
    echo "Delegated Task ID: " . $alert->delegated_task_id . "\n";

    if ($alert->delegatedTask) {
        echo "Task Title: " . $alert->delegatedTask->title . "\n";

        if ($alert->delegatedTask->assignee) {
            echo "Assignee Full Name: " . ($alert->delegatedTask->assignee->full_name ?? 'N/A') . "\n";
            echo "Assignee First Name: " . ($alert->delegatedTask->assignee->first_name ?? 'N/A') . "\n";
            echo "Assignee Last Name: " . ($alert->delegatedTask->assignee->last_name ?? 'N/A') . "\n";
        } else {
            echo "No assignee found\n";
        }
    } else {
        echo "No delegated task found\n";
    }
} else {
    echo "No alert with delegated task found\n";
}
