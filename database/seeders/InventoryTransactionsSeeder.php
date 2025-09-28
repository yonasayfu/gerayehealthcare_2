<?php

namespace Database\Seeders;

use App\Models\InventoryTransaction;
use Illuminate\Database\Seeder;

class InventoryTransactionsSeeder extends Seeder
{
    public function run(): void
    {
        InventoryTransaction::factory()->count(30)->create();
    }
}
