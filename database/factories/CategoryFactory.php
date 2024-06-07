<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */

  protected $model = \App\Models\Category::class;

  public function definition(): array {
    return [
      'title' => fake()->sentence(),
      'order' => random_int(0, 5),
      'description' => fake()->paragraph(2),
      'image' => null,
    ];
  }
}
