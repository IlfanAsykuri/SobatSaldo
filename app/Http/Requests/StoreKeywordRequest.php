<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKeywordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'keyword'     => [
                'required',
                'string',
                'max:100',
                // Unique per user (IDOR: cegah duplikasi keyword untuk user yg sama)
                "unique:keyword_dictionaries,keyword,NULL,id,user_id,{$userId}",
            ],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'keyword.required'     => 'Kata kunci wajib diisi.',
            'keyword.unique'       => 'Kata kunci ini sudah ada di kamusmu.',
            'keyword.max'          => 'Kata kunci maksimal 100 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
        ];
    }
}
