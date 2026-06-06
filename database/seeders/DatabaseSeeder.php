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
            'password' => 'Admin#123',
        ]);

        $this->call([
            BlogPostSeeder::class,
            StorySeeder::class,
        ]);
    }
}
