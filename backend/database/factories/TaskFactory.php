<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => fake()->sentence(3, true),
            'description' => fake()->paragraph(),
            'due_date' => fake()->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d'),
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed'])
        ];
    }
}
