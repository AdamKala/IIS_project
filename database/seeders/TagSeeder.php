<?php

namespace Database\Seeders;

use App\Models\Tags;
use App\Models\TagsThread;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tags::create([
            'id' => 1,
            'name' => 'Lidé',
            'thread_id' => 1,
        ]);

        TagsThread::create([
            'tag_id' => 1,
            'thread_id' => 1
        ]);

        Tags::create([
            'id' => 2,
            'name' => 'Komunikace',
            'thread_id' => 1,
        ]);

        TagsThread::create([
            'tag_id' => 2,
            'thread_id' => 1
        ]);

        Tags::create([
            'id' => 3,
            'name' => 'Sport',
            'thread_id' => 2,
        ]);

        TagsThread::create([
            'tag_id' => 3,
            'thread_id' => 2
        ]);

        Tags::create([
            'id' => 4,
            'name' => 'Soutěž',
            'thread_id' => 2,
        ]);

        TagsThread::create([
            'tag_id' => 4,
            'thread_id' => 2
        ]);

        Tags::create([
            'id' => 5,
            'name' => 'Sport',
            'thread_id' => 3,
        ]);

        TagsThread::create([
            'tag_id' => 5,
            'thread_id' => 3
        ]);

        Tags::create([
            'id' => 6,
            'name' => 'Vybavení',
            'thread_id' => 3,
        ]);

        TagsThread::create([
            'tag_id' => 6,
            'thread_id' => 3
        ]);

        Tags::create([
            'id' => 7,
            'name' => 'Humor',
            'thread_id' => 4,
        ]);

        TagsThread::create([
            'tag_id' => 7,
            'thread_id' => 4
        ]);

        Tags::create([
            'id' => 8,
            'name' => 'Kontroverze',
            'thread_id' => 5,
        ]);

        TagsThread::create([
            'tag_id' => 8,
            'thread_id' => 5
        ]);
    }
}
