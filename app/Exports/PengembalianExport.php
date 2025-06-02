<?php

namespace App\Exports;

use App\Models\DetailPengembalian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PengembalianExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DetailPengembalian::with(['barang', 'peminjaman.user'])
            ->where('soft_delete', 0)
            ->get()
            ->map(function ($pengembalian) {
                return [
                    'Nama Peminjam'     => $pengembalian->peminjaman->user->username ?? '-',
                    'Nama Barang'       => $pengembalian->barang->nama_barang ?? '-',
                    'Jumlah'            => $pengembalian->jumlah ?? '-',
                    'Tanggal Pinjam'    => $pengembalian->tanggal_pinjam ?? '-',
                    'Tanggal Kembali'   => $pengembalian->tanggal_kembali ?? '-',
                    'Tanggal Dikembalikan' => $pengembalian->tanggal_pengembalian ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama Peminjam',
            'Nama Barang',
            'Jumlah',
            'Tanggal Pinjam',
            'Tanggal Kembali',
            'Tanggal Dikembalikan',
        ];
    }
}
