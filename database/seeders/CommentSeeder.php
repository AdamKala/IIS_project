<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::create([
            'author' => 'Admin',
            'text' => 'Dobrý komentář!',
            'thread_id' => 1,  // Replace with the actual thread_id
            'created_by' => 1, // Replace with the actual user ID
            'rating' => 1,
        ]);

        Comment::create([
            'author' => 'Tester',
            'text' => 'Zatím to vypadá na Gravity Team L.',
            'thread_id' => 3,  // Replace with the actual thread_id
            'created_by' => 2, // Replace with the actual user ID
            'rating' => -2,
        ]);

        Comment::create([
            'author' => 'Pepa',
            'text' => 'Co se objevuje na velmi malých plážích? Mikrovlny.',
            'thread_id' => 4,  // Replace with the actual thread_id
            'created_by' => 3, // Replace with the actual user ID
            'rating' => 5,
        ]);
    }
}
