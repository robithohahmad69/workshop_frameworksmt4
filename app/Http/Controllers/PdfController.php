<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function landscape()
    {
        $bukus = Buku::with('kategori')->get();

        $pdf = Pdf::loadView('pdf.landscape', compact('bukus'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('landscape.pdf');
    }

    public function portrait()
    {
        $bukus = Buku::with('kategori')->get();

        $pdf = Pdf::loadView('pdf.portrait', compact('bukus'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('portrait.pdf');
    }
}