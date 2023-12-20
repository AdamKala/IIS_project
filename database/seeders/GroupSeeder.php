<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Group::create([
            'id' => 1,
            'name' => 'Facebook',
            'slug' => 'facebook',
            'enabled' => 1,
            'image_path' => '',
            'description' => 'Facebook, který není na facebooku.',
            'created_by' => 1, // Replace with the actual user ID
        ]);

        Group::create([
            'id' => 2,
            'name' => 'Sportovní nadšenci',
            'slug' => 'sportovninadsenci',
            'enabled' => 1,
            'image_path' => '',
            'description' => 'Skupina pro všechny nadšence jakéhokoliv sportu.',
            'created_by' => 2, // Replace with the actual user ID
        ]);

        Group::create([
            'id' => 3,
            'name' => 'Web developers',
            'slug' => 'webdevelopers',
            'enabled' => 1,
            'image_path' => '',
            'description' => 'Pojďme si navzájem pomoct a k něčemu se přiučit. Knowledge is power :)',
            'created_by' => 2, // Replace with the actual user ID
        ]);

        Group::create([
            'id' => 4,
            'name' => 'Srandičky tvojí tetičky',
            'slug' => 'srandickytvojiteticky',
            'enabled' => 1,
            'image_path' => '',
            'description' => 'Vtip je vtip.',
            'created_by' => 3, // Replace with the actual user ID
        ]);

        Group::create([
            'id' => 5,
            'name' => 'Let\'s hear your opinions',
            'slug' => 'letshearyouropinions',
            'enabled' => 1,
            'image_path' => '',
            'description' => 'Let\'s hear each other opinions. But please be respectful <3',
            'created_by' => 4, // Replace with the actual user ID
        ]);
    }
}
