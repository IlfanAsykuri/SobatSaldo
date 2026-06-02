<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKeywordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $userId    = auth()->id();
        $keywordId = $this->route('keyword'); // ID dari route parameter

        return [
            'keyword'     => [
                'required',
                'string',
                'max:100',
                // Ignore record ini sendiri saat validasi unique
                "unique:keyword_dictionaries,keyword,{$keywordId},id,user_id,{$userId}",
            ],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'keyword.required'     => 'Kata kunci wajib diisi.',
            'keyword.unique'       => 'Kata kunci ini sudah ada di kamusmu.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak valid.',
        ];
    }
}
