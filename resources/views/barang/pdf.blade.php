<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    table.label-table {
        width: 202mm;
        border-collapse: separate;
        border-spacing: 2mm 2mm;
        table-layout: fixed;
        margin: 0 auto;
    }

    table.label-table td {
        width: 38mm;
        height: 18mm;
        border: 0px solid #000;
        text-align: center;
        vertical-align: middle;
        padding: 0;
        overflow: hidden;
    }

    .label-isi {
        display: block;
        width: 100%;
        text-align: center;
    }

    .label-nama {
        font-size: 7pt;
        font-weight: bold;
        line-height: 1.2;
    }

    .label-harga {
        font-size: 9pt;
        font-weight: bold;
        color: #000;
        line-height: 1.2;
    }

    .label-id {
        font-size: 5pt;
        color: #888;
        line-height: 1.2;
    }

    .label-kosong { }
</style>
</head>
<body>

<?php
    $totalSlot = 40;
    $slots = array_fill(0, $totalSlot, null);

    $posisi = $startIndex;
    foreach ($barangs as $b) {
        if ($posisi >= $totalSlot) break;
        $slots[$posisi] = $b;
        $posisi++;
    }

    $baris = array_chunk($slots, 5);
?>

<table class="label-table">
    @foreach($baris as $row)
    <tr>
        @foreach($row as $slot)
        <td>
            @if($slot !== null)
                <div class="label-isi">
                    <div class="label-nama">{{ $slot->nama }}</div>
                    <div class="label-harga">Rp {{ number_format($slot->harga, 0, ',', '.') }}</div>
                    <div class="label-id">{{ $slot->id_barang }}</div>
                </div>
            @else
                <div class="label-kosong"></div>
            @endif
        </td>
        @endforeach
    </tr>
    @endforeach
</table>

</body>
</html>