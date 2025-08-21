<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use App\Models\TodoItem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();

        Todo::factory(5)
            ->create()
            ->each(function ($todo) use ($users) {
                $members = $users->random(rand(2, 5));
                $todo->members()->attach($members->pluck('id'));

                TodoItem::factory(rand(5, 15))
                    ->make()
                    ->each(function ($item) use ($todo, $members) {
                        $item->user_id = $members->random()->id;
                        $item->todo_id = $todo->id;
                        $item->save();
                    });
            });
    }
}
