<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function sertifikat()
    {
        $bukus = Buku::with('kategori')->get();

        $pdf = Pdf::loadView('pdf.sertifikat', compact('bukus'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('sertifikat.pdf');
    }

    public function surat()
    {
        $bukus = Buku::with('kategori')->get();

        $pdf = Pdf::loadView('pdf.surat', compact('bukus'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('surat.pdf');
    }
}