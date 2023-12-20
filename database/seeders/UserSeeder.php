<?php

namespace Database\Seeders;

use App\Models\GroupUser;
use App\Models\User;
use App\Models\UserHasRoles;
use App\Models\UserManageGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'about_me' => 'Jsem Admin této sociální sítě. Rád programuji, spravuji weby a hraji počítačové hry. Ve
            volném čase také rád zajdu do posilovny.',
        ]);

        UserHasRoles::create([
            'role_id' => 1,
            'model_type' => 'App\Models\User',
            'model_id' => 1,
        ]);

        UserHasRoles::create([
            'role_id' => 4,
            'model_type' => 'App\Models\User',
            'model_id' => 1,
            'group_id' => 1,
        ]);

        UserManageGroup::create([
            'id' => 1,
            'group_id' => 1,
            'user_id' => 1,
            'role_type' => 'spravce',
        ]);

        GroupUser::create([
            'id' => 1,
            'user_id' => 1,
            'group_id' => 1,
        ]);

        User::create([
            'id' => 2,
            'name' => 'Tester',
            'email' => 'tester@tester.com',
            'password' => Hash::make('tester'),
            'about_me' => 'Byl jsem pověřen testováním téhle stránky.',
        ]);

        UserHasRoles::create([
            'role_id' => 2,
            'model_type' => 'App\Models\User',
            'model_id' => 2,
            'group_id' => null,
        ]);

        UserHasRoles::create([
            'role_id' => 4,
            'model_type' => 'App\Models\User',
            'model_id' => 2,
            'group_id' => 2,
        ]);

        UserHasRoles::create([
            'role_id' => 4,
            'model_type' => 'App\Models\User',
            'model_id' => 2,
            'group_id' => 3,
        ]);

        UserManageGroup::create([
            'id' => 2,
            'group_id' => 2,
            'user_id' => 2,
            'role_type' => 'spravce',
        ]);

        UserManageGroup::create([
            'id' => 3,
            'group_id' => 3,
            'user_id' => 2,
            'role_type' => 'spravce',
        ]);

        GroupUser::create([
            'id' => 2,
            'user_id' => 2,
            'group_id' => 2,
        ]);

        GroupUser::create([
            'id' => 3,
            'user_id' => 2,
            'group_id' => 3,
        ]);

        User::create([
            'id' => 3,
            'name' => 'Pepa',
            'email' => 'pepa@seznam.cz',
            'password' => Hash::make('123456789'),
            'about_me' => 'Čau. Jsem Pepa. Těší mě.',
        ]);

        UserHasRoles::create([
            'role_id' => 2,
            'model_type' => 'App\Models\User',
            'model_id' => 3,
            'group_id' => null,
        ]);

        UserHasRoles::create([
            'role_id' => 4,
            'model_type' => 'App\Models\User',
            'model_id' => 3,
            'group_id' => 4
        ]);

        UserManageGroup::create([
            'id' => 4,
            'group_id' => 4,
            'user_id' => 3,
            'role_type' => 'spravce',
        ]);

        GroupUser::create([
            'id' => 4,
            'user_id' => 3,
            'group_id' => 4,
        ]);

        User::create([
            'id' => 4,
            'name' => 'Ema',
            'email' => 'emaishere@gmail.com',
            'password' => Hash::make('ema123456'),
            'about_me' => 'Hey there! Hope we will get to know each other.',
        ]);

        UserHasRoles::create([
            'role_id' => 2,
            'model_type' => 'App\Models\User',
            'model_id' => 4,
            'group_id' => null,
        ]);

        UserHasRoles::create([
            'role_id' => 4,
            'model_type' => 'App\Models\User',
            'model_id' => 4,
            'group_id' => 5,
        ]);

        UserManageGroup::create([
            'id' => 5,
            'group_id' => 5,
            'user_id' => 4,
            'role_type' => 'spravce',
        ]);

        GroupUser::create([
            'id' => 5,
            'user_id' => 4,
            'group_id' => 5,
        ]);

        GroupUser::create([
            'id' => 6,
            'user_id' => 2,
            'group_id' => 4,
        ]);

        UserHasRoles::create([
            'role_id' => 3,
            'model_type' => 'App\Models\User',
            'model_id' => 2,
            'group_id' => 4,
        ]);

        GroupUser::create([
            'id' => 7,
            'user_id' => 4,
            'group_id' => 1,
        ]);

        UserHasRoles::create([
            'role_id' => 3,
            'model_type' => 'App\Models\User',
            'model_id' => 4,
            'group_id' => 1,
        ]);

        User::create([
            'id' => 5,
            'name' => 'Fanda',
            'email' => 'fanda@gmail.com',
            'password' => Hash::make('fanda123456'),
            'about_me' => 'Ahoj. Já jsem Fanda. Rád vás poznávám.',
        ]);

        UserHasRoles::create([
            'role_id' => 2,
            'model_type' => 'App\Models\User',
            'model_id' => 5,
            'group_id' => null,
        ]);

        User::create([
            'id' => 6,
            'name' => 'Marek',
            'email' => 'marek@gmail.com',
            'password' => Hash::make('marek123456'),
            'about_me' => 'Mám rád sport a vlaky.',
        ]);

        UserHasRoles::create([
            'role_id' => 2,
            'model_type' => 'App\Models\User',
            'model_id' => 6,
            'group_id' => null,
        ]);

        UserHasRoles::create([
            'role_id' => 5,
            'model_type' => 'App\Models\User',
            'model_id' => 6,
            'group_id' => 5,
        ]);

        GroupUser::create([
            'id' => 8,
            'user_id' => 6,
            'group_id' => 5,
        ]);

        UserManageGroup::create([
            'id' => 6,
            'group_id' => 5,
            'user_id' => 6,
            'role_type' => 'moderator',
        ]);
    }
}
