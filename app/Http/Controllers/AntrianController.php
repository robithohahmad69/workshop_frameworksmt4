<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    // =====================
    // HALAMAN-HALAMAN VIEW
    // =====================

    // Halaman form pendaftaran tamu
    public function guestIndex()
    {
        return view('antrian.guest');
    }

    // Halaman tiket (tab baru setelah daftar)
    public function tiket($id)
    {
        $antrian = Antrian::findOrFail($id);
        return view('antrian.tiket', compact('antrian'));
    }

    // Halaman admin
    public function adminIndex()
    {
        return view('antrian.admin');
    }

    // Halaman papan antrian publik
    public function papan()
    {
        return view('antrian.papan');
    }

    // =====================
    // AKSI / LOGIC
    // =====================

    // Guest daftar antrian
    public function daftar(Request $request)
    {
        // Validate and return JSON error if validation fails
        $validator = \Validator::make($request->all(), [
            'nama' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Nama wajib diisi',
                'errors' => $validator->errors()
            ], 422);
        }

        // Ambil nomor terakhir hari ini, lalu +1
        $nomorTerakhir = Antrian::whereDate('created_at', today())->max('nomor') ?? 0;
        $nomorBaru = $nomorTerakhir + 1;

        $antrian = Antrian::create([
            'nomor' => $nomorBaru,
            'nama'  => $request->nama,
            'status' => 'menunggu',
        ]);

        // Simpan state terbaru ke Cache agar SSE bisa baca
        $this->updateCache();

        // Redirect ke halaman tiket di tab baru (dihandle JS di guest.blade.php)
        return response()->json([
            'success' => true,
            'tiket_url' => route('antrian.tiket', $antrian->id),
        ]);
    }

    // Admin panggil antrian berikutnya
    public function panggil(Request $request)
    {
        // Ambil antrian pertama yang masih 'menunggu'
        $antrian = Antrian::where('status', 'menunggu')
                          ->orderBy('nomor')
                          ->first();

        if (!$antrian) {
            return response()->json(['message' => 'Tidak ada antrian menunggu'], 404);
        }

        // Tandai yang sebelumnya 'dipanggil' jadi 'terlambat'
        Antrian::where('status', 'dipanggil')->update(['status' => 'terlambat']);

        // Set antrian ini jadi 'dipanggil'
        $antrian->update(['status' => 'dipanggil']);

        $this->updateCache();

        return response()->json(['success' => true]);
    }

    // Admin panggil ulang antrian yang terlambat (double click / tombol khusus)
    public function panggilTerlambat(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $antrian = Antrian::where('id', $request->id)
                          ->where('status', 'terlambat')
                          ->firstOrFail();

        // Tandai yang sedang 'dipanggil' jadi 'terlambat' dulu
        Antrian::where('status', 'dipanggil')->update(['status' => 'terlambat']);

        $antrian->update(['status' => 'dipanggil']);

        $this->updateCache();

        return response()->json(['success' => true]);
    }

    // Admin selesaikan antrian yang sedang dipanggil
    public function selesai(Request $request)
    {
        Antrian::where('status', 'dipanggil')->update(['status' => 'selesai']);

        $this->updateCache();

        return response()->json(['success' => true]);
    }

    // =====================
    // SSE STREAM
    // =====================

  public function stream(Request $request)
{
    // Tutup session agar tidak blocking request lain (fix session locking)
    $request->session()->save();

    // Bersihkan output buffer
    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    ignore_user_abort(true);
    set_time_limit(0);

    return response()->stream(function () {
        while (true) {
            // Ambil data terbaru dari cache
            $currentData = Cache::get('antrian_state', $this->buildState());

            // Kirim event SSE
            echo 'event: antrian-update' . PHP_EOL;
            echo 'data: ' . json_encode($currentData) . PHP_EOL;
            echo PHP_EOL;

            // Kirim ke browser segera
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();

            // Cek apakah client masih terhubung
            if (connection_aborted()) {
                break;
            }

            sleep(1);
        }
    }, 200, [
        'Content-Type'      => 'text/event-stream',
        'Cache-Control'     => 'no-cache',
        'X-Accel-Buffering' => 'no',
    ]);
}
    // =====================
    // HELPER PRIVATE
    // =====================

    // Ambil state antrian dari DB dan simpan ke Cache
    private function updateCache()
    {
        $state = $this->buildState();
        Cache::put('antrian_state', $state, now()->addHours(8));
    }

    // Bangun array state lengkap dari DB
    private function buildState(): array
    {
        return [
            'dipanggil' => Antrian::where('status', 'dipanggil')->orderBy('nomor')->get(['id','nomor','nama']),
            'menunggu'  => Antrian::where('status', 'menunggu')->orderBy('nomor')->get(['id','nomor','nama']),
            'terlambat' => Antrian::where('status', 'terlambat')->orderBy('nomor')->get(['id','nomor','nama']),
        ];
    }
}