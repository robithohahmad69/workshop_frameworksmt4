<!DOCTYPE html>
<html>
<head>
    <title>Laporan Buku (Landscape)</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
        }

        h1 {
            font-size: 35px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
        }
    </style>
</head>
<body>

<h1>SERTIFIKAT DATA BUKU</h1>
<p>Daftar Buku Perpustakaan</p>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Judul</th>
            <th>Pengarang</th>
            <th>Kategori</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bukus as $buku)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $buku->kode }}</td>
            <td>{{ $buku->judul }}</td>
            <td>{{ $buku->pengarang }}</td>
            <td>{{ $buku->kategori->nama_kategori }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>