<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */

  protected $model = \App\Models\Tag::class;

  public function definition(): array {
    $title = $this->faker->word(2);

    return [
      'title' => rtrim($title, '.'),
      'slug' => Str::slug($title, '-'),
      'description' => $this->faker->paragraph(1),
    ];
  }
}
