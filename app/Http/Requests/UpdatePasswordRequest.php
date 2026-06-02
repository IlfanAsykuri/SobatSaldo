<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'current_password'      => ['required', 'current_password'],
            'password'              => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'      => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required'              => 'Kata sandi baru wajib diisi.',
            'password.confirmed'             => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min'                   => 'Kata sandi baru minimal 8 karakter.',
            'password_confirmation.required' => 'Konfirmasi kata sandi wajib diisi.',
        ];
    }
}
