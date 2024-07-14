<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    $title = trim($this->faker->sentence(2), '.');
    return [
      "user_id" => 1,
      "title" => $title,
      "url" => str_replace(" ", "-", $title),
      "description" => fake()->paragraph(),
      "duration" => 15,
      "buffer_zone" => 0,
      "price" => null,
      "color" => null,
      "status" => 1,
      "created_at" => now(),
      "updated_at" => now(),
    ];
  }

  public function configure() {
    // \Illuminate\Database\Eloquent\Model
    // Database\Factories\Appointment
    return $this->afterCreating(function ($appointment) {
      // Adjust the number of settings you want to create per appointment
      \App\Models\AppointmentSetting::factory()->count(7)->create([
        'appointment_id' => $appointment->id,
      ]);
    });
  }
}
