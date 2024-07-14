<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory {
  /**
   * The current password being used by the factory.
   */
  protected static ?string $password;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    $images = collect(["user-1.jpg", "user-2.jpg", "user-3.jpg"]);
    return [
      'username' => fake()->userName(),
      'password' => Hash::make('123'),
      'first_name' => fake()->name(),
      'last_name' => fake()->name(),
      'email' => fake()->unique()->safeEmail(),
      'image' => $images->random(),
      'permission' => 1,
      // 'email_verified_at' => now(),
      'remember_token' => Str::random(10),
    ];
  }

  /**
   * Indicate that the model's email address should be unverified.
   */
  // public function unverified(): static {
  //   return $this->state(fn (array $attributes) => [
  //     'email_verified_at' => null,
  //   ]);
  // }
  public function is_admin(): static {
    return $this->state(fn (array $attributes) => [
      'permission' => 4,
    ]);
  }
}
