<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
          'email' => 'admin@gmail.com',
          'password' => bcrypt('12345678'),
          'role' => 'admin',
          'name' => 'Admin',
          'phone' => '081234567890'
        ]);
    }
}
