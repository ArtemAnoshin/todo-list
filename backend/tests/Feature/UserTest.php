<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_authenticated_user(): void
    {
        // Подготовка: создаем пользователя и получаем токен
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        // Получаем токен через login
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('access_token');

        // Выполнение: запрашиваем данные пользователя с токеном
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/user');

        // Проверки
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'user'
            ]);
    }
}
