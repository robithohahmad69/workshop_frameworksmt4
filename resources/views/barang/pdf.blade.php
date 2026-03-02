<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    /*
     * CATATAN PENTING: dompdf tidak support CSS Grid dan Flexbox penuh.
     * Kita pakai <table> HTML biasa untuk layout label.
     *
     * Kertas A4: 210mm x 297mm
     * Label TnJ 108: 5 kolom x 8 baris
     * Estimasi ukuran per label: lebar ~38mm, tinggi ~34mm
     * Margin kertas: disesuaikan agar pas
     */

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 8mm 5mm; /* margin atas-bawah dan kiri-kanan kertas */
    }

    table.label-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Paksa lebar kolom sama rata */
    }

    table.label-table td {
        width: 20%;       /* 100% / 5 kolom = 20% per kolom */
        height: 33mm;     /* Tinggi setiap label */
        border: 1px dashed #ccc;
        text-align: center;
        vertical-align: middle;
        padding: 3mm;
        word-wrap: break-word;
    }

    /* Label yang terisi data */
    .label-isi { }

    .label-nama {
        font-size: 8pt;
        font-weight: bold;
        margin-bottom: 3mm;
        line-height: 1.2;
    }

    .label-harga {
        font-size: 13pt;
        font-weight: bold;
        color: #000;
    }

    .label-id {
        font-size: 6pt;
        color: #888;
        margin-top: 2mm;
    }

    /* Label kosong (terlewati atau tidak ada data) */
    .label-kosong {
        /* Biarkan kosong, border sudah ada dari <td> */
    }
</style>
</head>
<body>

<?php
    /*
     * LOGIKA PENEMPATAN LABEL:
     *
     * Kertas punya 40 slot (5 kolom x 8 baris).
     * Kita buat array 40 elemen, default null (kosong).
     * Mulai dari $startIndex, isi dengan data barang.
     *
     * Contoh: X=3, Y=2 → startIndex = (2-1)*5 + (3-1) = 7
     * Artinya slot 0–6 kosong, slot 7 mulai diisi.
     *
     * Setelah diisi, pecah array 40 elemen jadi 8 kelompok
     * masing-masing 5 elemen (= 8 baris tabel).
     */

    $totalSlot = 40; // 5 x 8

    // Buat array 40 slot, semua null dulu
    $slots = array_fill(0, $totalSlot, null);

    // Isi slot mulai dari $startIndex
    $posisi = $startIndex;
    foreach ($barangs as $b) {
        if ($posisi >= $totalSlot) break; // Kertas sudah penuh, stop
        $slots[$posisi] = $b;
        $posisi++;
    }

    // Pecah 40 slot jadi baris-baris isi 5 per baris
    $baris = array_chunk($slots, 5);
?>

<table class="label-table">
    @foreach($baris as $row)
    <tr>
        @foreach($row as $slot)
        <td>
            @if($slot !== null)
                {{-- Ada data barang → tampilkan label --}}
                <div class="label-isi">
                    <div class="label-nama">{{ $slot->nama }}</div>
                    <div class="label-harga">Rp {{ number_format($slot->harga, 0, ',', '.') }}</div>
                    <div class="label-id">{{ $slot->id_barang }}</div>
                </div>
            @else
                {{-- Kosong → biarkan kosong --}}
                <div class="label-kosong"></div>
            @endif
        </td>
        @endforeach
    </tr>
    @endforeach
</table>

</body>
</html>