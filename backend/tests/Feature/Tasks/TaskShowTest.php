<?php

namespace Tests\Feature\Tasks;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AuthTrait;
use App\Models\Task;
use App\Models\User;

class TaskShowTest extends TestCase
{
    use RefreshDatabase, AuthTrait;

    public function test_guest_cannot_view_task(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(401);
    }

    public function test_owner_can_view_task(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $task->id, 'user_id' => $user->id]);
    }

    public function test_user_cannot_view_other_users_task(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $otherUser = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_view_any_task(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $adminToken = $this->getTokenForUser($admin);

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken
        ])->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $task->id, 'user_id' => $user->id]);
    }

    public function test_non_admin_cannot_view_other_users_task(): void
    {
        $user1 = User::factory()->create(['password' => bcrypt('password')]);
        $user2 = User::factory()->create(['password' => bcrypt('password')]);
        $token1 = $this->getTokenForUser($user1);

        $task = Task::factory()->create(['user_id' => $user2->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1
        ])->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403)
            ->assertJson(['error' => 'Forbidden']);
    }
}
