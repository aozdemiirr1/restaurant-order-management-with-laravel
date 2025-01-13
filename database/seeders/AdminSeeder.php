<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'Saklı Saray Admin',
            'email' => 'saklisaray@admin.com',
            'password' => Hash::make('saklisaray5334'),
        ]);
    }
}
