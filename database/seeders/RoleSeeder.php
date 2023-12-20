<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'web']);

        foreach (Permission::all() as $permission) {
            $admin->givePermissionTo($permission->name);
        }

        $registered = Role::create(['name' => 'Registrovaný uživatel', 'guard_name' => 'web']);
        $permissions = ['view groups', 'create groups', 'join groups',
            'view threads', 'view comments'];

        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $registered->givePermissionTo($permission);
            }
        }

        $groupmember = Role::create(['name' => 'Člen skupiny', 'guard_name' => 'web']);
        $permissions = ['view groups', 'create groups', 'join groups',
            'view threads', 'create threads', 'delete own threads', 'edit own threads', 'toggle own threads',
            'view comments', 'create comments', 'delete own comments', 'toggle own comments'];

        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $groupmember->givePermissionTo($permission);
            }
        }

        $groupmanager = Role::create(['name' => 'Správce skupiny', 'guard_name' => 'web']);
        $permissions = ['accept users', 'remove users', 'edit user roles',
            'view groups', 'create groups', 'join groups', 'delete own groups', 'edit own groups', 'toggle own groups',
            'view threads', 'create threads', 'delete own threads', 'delete threads', 'edit threads', 'edit own threads', 'toggle own threads', 'toggle threads',
            'view comments', 'create comments', 'delete own comments', 'toggle own comments', 'delete comments', 'toggle comments'];

        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $groupmanager->givePermissionTo($permission);
            }
        }

        $groupmanager2 = Role::create(['name' => 'Moderátor skupiny', 'guard_name' => 'web']);
        $permissions = ['view groups', 'create groups', 'join groups', 'delete own groups', 'edit own groups', 'toggle own groups',
            'view threads', 'create threads', 'delete own threads', 'delete threads', 'edit threads', 'edit own threads', 'toggle own threads', 'toggle threads',
            'view comments', 'create comments', 'delete own comments', 'toggle own comments', 'delete comments', 'toggle comments'];

        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $groupmanager2->givePermissionTo($permission);
            }
        }

        $unregistered = Role::create(['name' => 'Neregistrovaný uživatel', 'guard_name' => 'web']);
        $permissions = ['view groups', 'view threads', 'view comments'];
        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $unregistered->givePermissionTo($permission);
            }
        }

    }
}
