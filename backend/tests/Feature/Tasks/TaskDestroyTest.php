<?php

namespace Tests\Feature\Tasks;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AuthTrait;
use App\Models\Task;
use App\Models\User;

class TaskDestroyTest extends TestCase
{
    use RefreshDatabase, AuthTrait;

    public function test_guest_cannot_delete_task(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(401);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_owner_can_delete_task(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_delete_other_users_task(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $otherUser = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_admin_can_delete_any_task(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $adminToken = $this->getTokenForUser($admin);

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_deleted_task_no_longer_accessible(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $task = Task::factory()->create(['user_id' => $user->id]);

        // Удаляем задачу
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/tasks/{$task->id}");

        // Пытаемся получить удаленную задачу
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(404); // задача не найдена
    }
}
