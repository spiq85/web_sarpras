<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang</title>
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
    <h2>Laporan Data Barang</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $barang)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $barang->nama_barang }}</td>
                <td>{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                <td>{{ $barang->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
