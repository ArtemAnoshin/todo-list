<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run()
    {
        // Получаем обычных пользователей (не администраторов)
        $users = User::where('role', 'user')->limit(10)->get();

        foreach ($users as $user) {
            // Создаем по 10 задач для каждого пользователя
            Task::factory()->count(10)->create([
                'user_id' => $user->id
            ]);
        }
    }
}
