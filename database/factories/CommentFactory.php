<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\comment>
 */
class CommentFactory extends Factory {

  protected $model = \App\Models\Comment::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    return [
      'reply_on' => null,
      'user_id' => 2,
      'article_id' => null,
      'text' => fake()->paragraph(),
    ];
  }

  public function user($id): static {
    return $this->state(fn (array $attributes) => [
      'user_id' => $id,
    ]);
  }

  public function article($id): static {
    return $this->state(fn (array $attributes) => [
      'article_id' => $id,
    ]);
  }

  public function reply($id): static {
    return $this->state(fn (array $attributes) => [
      'reply_on' => $id,
    ]);
  }
}
