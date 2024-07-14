<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AppointmentSettingFactory extends Factory {
  public function definition(): array {

    $keys = ['0', '1', '2', '3', '4', '5', '6', 'excluded'];
    $key = $this->faker->randomElement($keys);

    if ($key == 'excluded') {
      $value = $this->faker->date('Y-m-d') . '|' .
        $this->faker->time('h:i:a') . '-' .
        $this->faker->time('h:i:a');
    } else {
      $value = $this->faker->time('h:i:a') . '-' .
        $this->faker->time('h:i:a');
    }

    return [
      // "appointment_id" => \App\Models\Appointment::factory(),
      "appointment_id" => null,
      "key" => $key,
      "value" => $value,
    ];
  }
}
