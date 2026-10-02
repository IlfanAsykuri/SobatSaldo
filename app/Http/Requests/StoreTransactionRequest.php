<?php
namespace App\Http\Requests;
use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $mode = $this->input('inputMode', 'reguler');

        // IDOR: dompet harus milik user yang login
        $ownWallet = Rule::exists('wallets', 'id')->where('user_id', $this->user()->id);

        $rules = [
            'wallet_id' => ['nullable', $ownWallet],
            'inputMode' => ['nullable', 'string', 'in:reguler,mutasi,hutang'],
        ];

        if ($mode === 'reguler') {
            $rules['raw_text'] = ['required', 'string', 'max:500'];
        } elseif ($mode === 'mutasi') {
            $rules['amount'] = ['required', 'numeric', 'min:1'];
            $rules['wallet_id'] = ['required', $ownWallet];
            $rules['to_wallet_id'] = ['required', $ownWallet, 'different:wallet_id'];
        } elseif ($mode === 'hutang') {
            $rules['amount'] = ['required', 'numeric', 'min:1'];
            $rules['wallet_id'] = ['required', $ownWallet];
            $rules['desc_hutang'] = ['required', 'string', 'max:255'];
            $rules['type'] = ['required', Rule::in(Transaction::DEBT_TYPES)];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'raw_text.required'      => 'Deskripsi transaksi wajib diisi.',
            'amount.required'        => 'Nominal wajib diisi.',
            'wallet_id.required'     => 'Dompet asal wajib dipilih.',
            'wallet_id.exists'       => 'Dompet tidak ditemukan.',
            'to_wallet_id.exists'    => 'Dompet tujuan tidak ditemukan.',
            'to_wallet_id.required'  => 'Dompet tujuan wajib dipilih.',
            'to_wallet_id.different' => 'Dompet asal dan tujuan tidak boleh sama.',
            'desc_hutang.required'   => 'Deskripsi / Nama wajib diisi.',
        ];
    }
}
