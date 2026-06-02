<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:50'],
            'type'           => ['required', 'in:bank,ewallet,cash'],
            'color_theme'    => ['required', 'in:emerald,blue,amber,rose,violet,slate'],
            'account_number' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Nama dompet wajib diisi.',
            'type.required'        => 'Jenis dompet wajib dipilih.',
            'type.in'              => 'Jenis dompet tidak valid.',
            'color_theme.required' => 'Warna kartu wajib dipilih.',
        ];
    }
}
