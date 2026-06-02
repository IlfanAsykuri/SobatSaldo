<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $mode = $this->input('inputMode', 'reguler');
        
        $rules = [
            'wallet_id' => ['nullable', 'exists:wallets,id'],
            'inputMode' => ['nullable', 'string', 'in:reguler,mutasi,hutang'],
        ];

        if ($mode === 'reguler') {
            $rules['raw_text'] = ['required', 'string', 'max:500'];
        } elseif ($mode === 'mutasi') {
            $rules['amount'] = ['required', 'numeric', 'min:1'];
            $rules['wallet_id'] = ['required', 'exists:wallets,id'];
            $rules['to_wallet_id'] = ['required', 'exists:wallets,id', 'different:wallet_id'];
        } elseif ($mode === 'hutang') {
            $rules['amount'] = ['required', 'numeric', 'min:1'];
            $rules['wallet_id'] = ['required', 'exists:wallets,id'];
            $rules['desc_hutang'] = ['required', 'string', 'max:255'];
            $rules['type'] = ['required', 'in:debt,receivable'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'raw_text.required'      => 'Deskripsi transaksi wajib diisi.',
            'amount.required'        => 'Nominal wajib diisi.',
            'wallet_id.required'     => 'Dompet asal wajib dipilih.',
            'to_wallet_id.required'  => 'Dompet tujuan wajib dipilih.',
            'to_wallet_id.different' => 'Dompet asal dan tujuan tidak boleh sama.',
            'desc_hutang.required'   => 'Deskripsi / Nama wajib diisi.',
        ];
    }
}
