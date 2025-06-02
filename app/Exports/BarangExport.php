<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Barang::with('kategori')->get()->map(function ($barang) {
            return [
                'Kode Barang'      => $barang->kode_barang,
                'Nama Barang'      => $barang->nama_barang,
                'Kategori'         => $barang->kategori->nama_kategori ?? '-',
                'Stok'             => $barang->stock,
                'Brand'            => $barang->brand,
                'Status'           => $barang->status,
                'Kondisi'          => $barang->kondisi_barang,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Stok',
            'Brand',
            'Status',
            'Kondisi',
        ];
    }
}
