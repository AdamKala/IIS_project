<?php

namespace Database\Seeders;

use App\Models\Thread;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Thread::create([
            'id' => 1,
            'name' => 'Vlákno pro Facebook',
            'text' => 'Text pro Facebook',
            'enabled' => 1,
            'slug' => 'vlaknofb',
            'group_id' => 1,
            'created_by' => 1,
        ]);

        Thread::create([
            'id' => 2,
            'name' => 'Turnaj v šipkách',
            'text' => 'V pátek chceme hrát turnaj. kdo se přidá?',
            'enabled' => 1,
            'slug' => 'turnajvsipkach',
            'group_id' => 2,
            'created_by' => 2,
        ]);

        Thread::create([
            'id' => 3,
            'name' => 'Nejlepší tenisová raketa',
            'text' => 'Chci začít hrát aktivně tenis a hledám nějakou dobrou tenisovou raketu. Má někdo z vás nějaké tipy?',
            'enabled' => 1,
            'slug' => 'nejlepsitenisovaraketa',
            'group_id' => 2,
            'created_by' => 2,
        ]);

        Thread::create([
            'id' => 4,
            'name' => 'Dad jokes',
            'text' => 'Tak se ukažte :)',
            'enabled' => 1,
            'slug' => 'dadjokes',
            'group_id' => 4,
            'created_by' => 3,
        ]);

        Thread::create([
            'id' => 5,
            'name' => 'Vidlička > nůž',
            'text' => 'Vidlička je podle mě jediný příbor, který je potřeba k jídlu. Nože jsou zbytečné.',
            'enabled' => 1,
            'slug' => 'vidlicka-nuz',
            'group_id' => 5,
            'created_by' => 4,
        ]);

    }
}
