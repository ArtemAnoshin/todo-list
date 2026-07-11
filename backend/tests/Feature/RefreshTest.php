<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RefreshTest extends TestCase
{
    use RefreshDatabase;

    public function test_refresh_token(): void
    {
        // Подготовка: создаем пользователя и получаем токен
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $oldToken = $loginResponse->json('access_token');

        // Выполнение: обновляем токен
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $oldToken
        ])->postJson('/api/auth/refresh');

        // Проверки
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);

        // Проверяем, что токен действительно новый
        $newToken = $response->json('access_token');
        $this->assertNotEquals($oldToken, $newToken);
    }

    public function test_refresh_without_token(): void
    {
        // Выполнение: пробуем refresh без токена
        $response = $this->postJson('/api/auth/refresh');

        // Проверки
        $response->assertStatus(401);
    }
}
