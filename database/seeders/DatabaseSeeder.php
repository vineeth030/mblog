<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'admin',
            'email'    => 'admin@mblog.test',
            'password' => 'password123',
        ]);

        $this->call(BlogPostSeeder::class);
    }
}
