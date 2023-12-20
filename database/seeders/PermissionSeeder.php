<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User permissions
        Permission::create(['name' => 'accept users', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit user roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'remove users', 'guard_name' => 'web']);

        // Groups permissions
        Permission::create(['name' => 'view groups', 'guard_name' => 'web']);
        Permission::create(['name' => 'create groups', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete own groups', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete groups', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit own groups', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit groups', 'guard_name' => 'web']);
        Permission::create(['name' => 'toggle own groups', 'guard_name' => 'web']);
        Permission::create(['name' => 'toggle groups', 'guard_name' => 'web']);
        Permission::create(['name' => 'join groups', 'guard_name' => 'web']);

        // Threads permissions
        Permission::create(['name' => 'view threads', 'guard_name' => 'web']);
        Permission::create(['name' => 'create threads', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete own threads', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete threads', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit own threads', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit threads', 'guard_name' => 'web']);
        Permission::create(['name' => 'toggle own threads', 'guard_name' => 'web']);
        Permission::create(['name' => 'toggle threads', 'guard_name' => 'web']);

        // Comments permissions
        Permission::create(['name' => 'view comments', 'guard_name' => 'web']);
        Permission::create(['name' => 'create comments', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete own comments', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete comments', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit own comments', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit comments', 'guard_name' => 'web']);
        Permission::create(['name' => 'toggle own comments', 'guard_name' => 'web']);
        Permission::create(['name' => 'toggle comments', 'guard_name' => 'web']);

        // Roles permissions
        Permission::create(['name' => 'view roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'create roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'assign roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'remove roles', 'guard_name' => 'web']);
    }
}
