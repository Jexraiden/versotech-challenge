<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Demo seeders must not be run in production.');
        }

        $this->call([
            CommerceDataSeeder::class,
            DemoUserSeeder::class,
        ]);
    }
}
