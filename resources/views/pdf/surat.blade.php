<!DOCTYPE html>
<html>
<head>
    <title>Surat Buku</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid black;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

<div class="header">
    <h2>PERPUSTAKAAN DIGITAL</h2>
    <p>Daftar Buku dan Kategori</p>
</div>

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