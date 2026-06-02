<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private int $userId) {}

    public function collection()
    {
        return Transaction::with(['category', 'wallet'])
            ->where('user_id', $this->userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Deskripsi', 'Jenis', 'Kategori', 'Dompet', 'Nominal (Rp)'];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d/m/Y H:i'),
            $row->raw_text,
            $row->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
            $row->category->name ?? 'Lain-lain',
            $row->wallet->name ?? '-',
            (float) $row->amount,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                  'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF059669']]],
        ];
    }
}
