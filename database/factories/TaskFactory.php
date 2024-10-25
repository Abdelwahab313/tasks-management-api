<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(Task::getStatuses()),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days')
        ];
    }

    // Custom states
    public function pending(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_PENDING
        ]);
    }

    public function completed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_COMPLETED
        ]);
    }
}
