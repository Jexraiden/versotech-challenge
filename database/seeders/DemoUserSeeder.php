<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Demo seeders must not be run in production.');
        }

        $demoUsers = [
            ['company_id' => 1, 'name' => 'Bom Preço', 'email' => 'bompreco@example.test'],
            ['company_id' => 2, 'name' => 'Atacadão Central', 'email' => 'atacadao@example.test'],
            ['company_id' => 3, 'name' => 'Empório São Jorge', 'email' => 'emporio@example.test'],
        ];

        foreach ($demoUsers as $demoUser) {
            Company::findOrFail($demoUser['company_id']);
            User::updateOrCreate(
                ['email' => $demoUser['email']],
                [
                    'company_id' => $demoUser['company_id'],
                    'name' => $demoUser['name'],
                    'password' => Hash::make('password'),
                ],
            );
        }
    }
}
