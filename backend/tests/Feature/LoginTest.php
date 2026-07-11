<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials(): void
    {
        // Подготовка: создаем пользователя
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Выполнение: отправляем запрос на логин
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Проверки
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);

        // Проверяем, что токен возвращается
        $this->assertArrayHasKey('access_token', $response->json());
    }

    public function test_login_with_invalid_credentials(): void
    {
        // Выполнение: отправляем неверные данные
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        // Проверки
        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized'
            ]);
    }
}
