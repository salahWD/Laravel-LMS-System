<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\article>
 */
class ArticleFactory extends Factory {

  protected $model = \App\Models\Article::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    $title = $this->faker->sentence(2);

    return [
      'user_id' => 1,
      'category_id' => $this->faker->randomElement([1, 2, 3]),
      'title' => rtrim($title, '.'),
      'description' => $this->faker->paragraph(1),
      'content' => $this->faker->paragraph(12),
    ];
  }

  public function auther($id): static {
    return $this->state(fn (array $attributes) => [
      'auther' => $id,
    ]);
  }

  public function category($id): static {
    return $this->state(fn (array $attributes) => [
      'category' => $id,
    ]);
  }
}
