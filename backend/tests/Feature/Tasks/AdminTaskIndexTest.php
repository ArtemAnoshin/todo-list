<?php

namespace Tests\Feature\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AuthTrait;

class AdminTaskIndexTest extends TestCase
{
    use RefreshDatabase, AuthTrait;

    public function test_admin_can_view_all_tasks(): void
    {
        // Создаем админа и обычного пользователя
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $user2 = User::factory()->create(['password' => bcrypt('password')]);
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);

        // Создаем задачи от разных пользователей
        Task::factory()->count(2)->create(['user_id' => $user->id]);
        Task::factory()->count(3)->create(['user_id' => $user2->id]);

        // Создаем задачи от админа (если админ может иметь задачи)
        Task::factory()->count(1)->create(['user_id' => $admin->id]);

        $adminToken = $this->getTokenForUser($admin);

        $response = $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(6, 'data');
    }
}
