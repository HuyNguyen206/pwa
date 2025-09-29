<?php

namespace Database\Seeders;

use App\Models\Meter;
use App\Models\Reading;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Tim Tech',
            'email' => 'tim@example.com',
        ]);

        Meter::factory()->count(8)->create()->each(function($meter){
            Reading::factory()->count(rand(2,5))->create(['meter_id' => $meter->id]);
        });
    }
}
