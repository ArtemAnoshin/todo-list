<?php

namespace Tests\Feature\Tasks;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AuthTrait;
use App\Models\User;

class TaskStoreTest extends TestCase
{
    use RefreshDatabase, AuthTrait;

    public function test_guest_cannot_create_task(): void
    {
        $response = $this->postJson('/api/tasks', []);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_task(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => '2023-12-31',
            'status' => 'pending'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'due_date',
                'status',
                'user_id',
                'created_at',
                'updated_at'
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => '2023-12-31 00:00:00', // TODO: Пока не разобрался, почему из базы приходит дата и время, а не просто дата
            'status' => 'pending',
            'user_id' => $user->id
        ]);
    }

    public function test_task_is_created_for_current_user(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/tasks', [
            'title' => 'My Task',
            'description' => 'Task Description',
            'status' => 'pending'
        ]);

        $taskId = $response->json('id');

        $this->assertDatabaseHas('tasks', [
            'id' => $taskId,
            'user_id' => $user->id
        ]);
    }

    public function test_validation_fails_with_invalid_data(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/tasks', [
            'title' => 'ab', // слишком короткий
            'status' => 'invalid_status', // неверный статус
            'due_date' => 'invalid-date' // неверная дата
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status', 'due_date']);
    }

    public function test_required_fields_validation(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = $this->getTokenForUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/tasks', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }
}
