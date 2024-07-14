<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookedAppointment>
 */
class BookedAppointmentFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {

    $users = \App\Models\User::notAdmin()->select("id")->get()->pluck("id");
    $appointments = \App\Models\Appointment::select("id")->get()->pluck("id");

    return [
      "booker_id" => $users->random(),
      "admin_id" => 1,
      "appointment_id" => $appointments->random(),
      "appointment_date" => Carbon::now()->subDays(rand(0, 7))->format('Y-m-d'),
      "status" => 1,
      "notes" => fake()->paragraph(2),
    ];
  }
}
