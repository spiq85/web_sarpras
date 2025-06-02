<?php

namespace App\Exports;

use App\Models\DetailPeminjaman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PeminjamanExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DetailPeminjaman::with(['barang', 'user'])->get()->map(function ($item) {
            return [
                'Nama Peminjam'     => $item->user->username ?? '-',
                'Nama Barang'       => $item->barang->nama_barang ?? '-',
                'Jumlah'            => $item->jumlah,
                'Keperluan'         => $item->keperluan,
                'Kelas'             => $item->class,
                'Tanggal Pinjam'    => $item->tanggal_pinjam,
                'Tanggal Kembali'   => $item->tanggal_kembali,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Peminjam',
            'Nama Barang',
            'Jumlah',
            'Keperluan',
            'Kelas',
            'Tanggal Pinjam',
            'Tanggal Kembali',
        ];
    }
}
