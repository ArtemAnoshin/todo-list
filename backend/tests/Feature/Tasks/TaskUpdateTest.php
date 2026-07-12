<?php

namespace Tests\Feature\Tasks;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AuthTrait;
use App\Models\Task;
use App\Models\User;

class TaskUpdateTest extends TestCase
{
    use RefreshDatabase, AuthTrait;

    public function test_guest_cannot_update_task(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $task = Task::factory()->create(['title' => 'Old Title', 'user_id' => $user->id]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'New Title'
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseHas('tasks', ['title' => 'Old Title']);
    }

    public function test_owner_can_update_task(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old Title',
            'status' => 'pending'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson("/api/tasks/{$task->id}", [
            'title' => 'New Title',
            'status' => 'completed',
            'description' => 'Updated description'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'New Title',
                'status' => 'completed',
                'description' => 'Updated description'
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'New Title',
            'status' => 'completed',
            'description' => 'Updated description'
        ]);
    }

    public function test_user_cannot_update_other_users_task(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $otherUser = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Original Title'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson("/api/tasks/{$task->id}", [
            'title' => 'Hacked Title'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['title' => 'Original Title']);
    }

    public function test_admin_can_update_any_task(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $adminToken = $this->getTokenForUser($admin);

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Original Title'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken
        ])->putJson("/api/tasks/{$task->id}", [
            'title' => 'Admin Updated Title'
        ]);

        $response->assertStatus(200)
            ->assertJson(['title' => 'Admin Updated Title']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Admin Updated Title'
        ]);
    }

    public function test_validation_fails_with_invalid_data(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson("/api/tasks/{$task->id}", [
            'title' => 'ab', // слишком короткий
            'status' => 'invalid_status' // неверный статус
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status']);
    }

    public function test_partial_update_works(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Original Title',
            'description' => 'Original Description',
            'status' => 'pending'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Title',
                'description' => 'Original Description', // не изменилось
                'status' => 'pending' // не изменилось
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Updated Title',
            'description' => 'Original Description',
            'status' => 'pending'
        ]);
    }
}
