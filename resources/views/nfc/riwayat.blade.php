@extends('layouts.apps')

@section('title', 'Riwayat Scan NFC')
@section('icon', 'mdi mdi-history')
@section('page-title', 'Riwayat Scan NFC')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Riwayat Scan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Riwayat Scan (50 Terakhir)</h4>
                    <div>
                        <a href="{{ route('nfc.index') }}" class="btn btn-gradient-success btn-sm">
                            <i class="mdi mdi-nfc"></i> Scanner NFC
                        </a>
                        <a href="{{ route('warga.index') }}" class="btn btn-gradient-primary btn-sm">
                            <i class="mdi mdi-account-multiple"></i> Daftar Warga
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Waktu Scan</th>
                                <th>Serial NFC</th>
                                <th>Nama Warga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $r)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $r->waktu_scan }}</td>
                                <td><code>{{ $r->serial_number }}</code></td>
                                <td>{{ $r->warga?->nama ?? '-' }}</td>
                                <td>
                                    @if($r->status === 'dikenal')
                                        <span class="badge badge-gradient-success">✅ Dikenal</span>
                                    @else
                                        <span class="badge badge-gradient-danger">❌ Tidak Dikenal</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Belum ada riwayat scan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection