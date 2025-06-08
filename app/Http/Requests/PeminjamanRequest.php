<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [ 
            'users_id'         => 'required|exists:users,id',
            'id_barang'        => 'required|exists:barang,id',
            'jumlah'           => 'required|integer|min:1',
            'keperluan'        => 'required|string|max:255',
            'class'            => 'required|string|max:100',
            'tanggal_pinjam'   => 'required|date|after_or_equal:today',
            'tanggal_kembali'  => 'required|date|after:tanggal_pinjam',
        ];
    }

    public function messages(): array
    {
        return [
            'users_id.required' => 'User wajib dipilih.',
            'id_barang.required' => 'Barang wajib dipilih.',
            'jumlah.min' => 'Jumlah minimal 1.',
            'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pinjam.',
        ];
    }
}
