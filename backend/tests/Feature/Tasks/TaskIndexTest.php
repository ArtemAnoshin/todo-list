<?php

namespace Tests\Feature\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AuthTrait;

class TaskIndexTest extends TestCase
{
    use RefreshDatabase, AuthTrait;

    public function test_authenticated_user_can_view_their_tasks(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        // Создаем задачи для пользователя
        Task::factory()->count(3)->create(['user_id' => $user]);

        $response = $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_user_cannot_view_other_users_tasks(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $user2 = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        // Создаем задачи для обоих пользователей
        Task::factory()->create(['user_id' => $user->id]); // своя задача
        Task::factory()->count(2)->create(['user_id' => $user2->id]); // чужие задачи

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data'); // только своя задача
    }
}
