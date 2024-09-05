<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DummyUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userData = [
            //data admin
            [
                'username' => 'admin',
                'password' => bcrypt('admin'),
                'role' => 'admin'
            ],
            [
                'username' => 'superAdmin',
                'password' => bcrypt('admin'),
                'role' => 'admin'
            ]
        ];
        foreach ($userData as $key => $val) {
            User::create($val);
        }
    }
}
