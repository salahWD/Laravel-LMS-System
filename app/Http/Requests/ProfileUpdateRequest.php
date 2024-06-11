<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest {
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
   */
  public function rules(): array {
    return [
      'first_name' => ['nullable', 'string', 'max:32'],
      'last_name' => ['nullable', 'string', 'max:32'],
      'profile_pic' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
    ];
  }
}
