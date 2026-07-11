<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_with_valid_token(): void
    {
        // Подготовка: создаем пользователя и получаем токен
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Получаем токен через login
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('access_token');

        // Выполнение: отправляем logout с токеном
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/logout');

        // Проверки
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out'
            ]);
    }

    public function test_logout_without_token(): void
    {
        // Выполнение: пробуем logout без токена
        $response = $this->postJson('/api/auth/logout');

        // Проверки
        $response->assertStatus(401);
    }
}
