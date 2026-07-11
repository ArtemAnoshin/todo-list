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

    protected User $user1;
    protected User $user2;
    protected string $token1;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем пользователей и получаем токены
        $this->user1 = User::factory()->create(['password' => bcrypt('password')]);
        $this->token1 = $this->getTokenForUser($this->user1);
        $this->user2 = User::factory()->create(['password' => bcrypt('password')]);
    }

    public function test_authenticated_user_can_view_their_tasks(): void
    {
        // Создаем задачи для пользователя
        Task::factory()->count(3)->create(['user_id' => $this->user1->id]);

        $response = $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token1
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_user_cannot_view_other_users_tasks(): void
    {
        // Создаем задачи для обоих пользователей
        Task::factory()->create(['user_id' => $this->user1->id]); // своя задача
        Task::factory()->count(2)->create(['user_id' => $this->user2->id]); // чужие задачи

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token1
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data'); // только своя задача
    }

    public function test_admin_can_view_all_tasks(): void
    {
        // Создаем админа и обычного пользователя
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);

        // Создаем задачи от разных пользователей
        Task::factory()->count(2)->create(['user_id' => $this->user1->id]);
        Task::factory()->count(3)->create(['user_id' => $this->user2->id]);

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
