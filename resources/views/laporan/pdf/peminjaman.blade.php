<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h2 { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 10px; font-size: 12px; }
        th { background-color: #dce6f1; text-align: center; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        td { text-align: center; }
    </style>
</head>
<body>
    <h2>Laporan Data Peminjaman</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama User</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->user->username ?? '-' }}</td>
                <td>{{ $p->barang->nama_barang ?? '-' }}</td>
                <td>{{ $p->jumlah }}</td>
                <td>{{ $p->tanggal_pinjam }}</td>
                <td>{{ $p->tanggal_kembali }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
