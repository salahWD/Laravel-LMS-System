<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest {
  /**
   * Determine if the user is authorized to make this request.
   */
  // public function authorize(): bool {
  // dd(auth()->check() && auth()->user()->is_admin());
  // return auth()->check() && auth()->user()->is_admin();
  //   return true;
  // }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array {
    return [
      "title" => ["required", "string"],
      "url_slug" => ["required", "string"],
      "description" => ["nullable", "string"],
      "price" => ["nullable", "numeric", "min:0"],
      "duration_minutes" => ["required", "numeric", "min:5", "max:1440"],
      "available_days" => ["required", "array", "min:7"],
      "timezone" => ["required", "timezone:all"],
      "available_days.*.status" => ["sometimes", "in:on,of,off"],
      "available_days.*.times" => ["nullable", "array", "min:1"],
      "available_days.*.times.*" => ["array", "min:6"],
      "available_days.*.times.*.from_hour" => ["required", "numeric", "min:1", "max:12"],
      "available_days.*.times.*.from_minut" => ["required", "numeric", "min:0", "max:59"],
      "available_days.*.times.*.from_format" => ["required", "string", "in:am,pm"],
      "available_days.*.times.*.to_hour" => ["required", "numeric", "min:1", "max:12"],
      "available_days.*.times.*.to_minut" => ["required", "numeric", "min:0", "max:59"],
      "available_days.*.times.*.to_format" => ["required", "string", "in:am,pm"],
      "excluded_days" => ["nullable", "array"],
      "excluded_days.times" => ["sometimes", "required", "array", "min:1"],
      "excluded_days.times.*" => ["array", "min:6"],
      "excluded_days.times.*.from_hour" => ["required", "numeric", "min:1", "max:12"],
      "excluded_days.times.*.from_minut" => ["required", "numeric", "min:0", "max:59"],
      "excluded_days.times.*.from_format" => ["required", "string", "in:am,pm"],
      "excluded_days.times.*.to_hour" => ["required", "numeric", "min:1", "max:12"],
      "excluded_days.times.*.to_minut" => ["required", "numeric", "min:0", "max:59"],
      "excluded_days.times.*.to_format" => ["required", "string", "in:am,pm"],
    ];
  }
}
