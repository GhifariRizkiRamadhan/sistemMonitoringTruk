<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Menambahkan user admin
        DB::table('users')->insert([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'created_at' => now()
        ]);

        // Menambahkan user owner
        DB::table('users')->insert([
            'username' => 'owner',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
            'created_at' => now()
        ]);
    }
}