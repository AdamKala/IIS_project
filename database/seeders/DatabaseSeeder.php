<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Group;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            GroupSeeder::class,
            ThreadSeeder::class,
            CommentSeeder::class,
            UserSeeder::class,
            TagSeeder::class,
        ]);
    }
}
