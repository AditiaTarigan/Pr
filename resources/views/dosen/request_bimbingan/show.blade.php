@extends('layouts.app')

@section('title', 'Detail Request Bimbingan')

@section('content')
<div class="container py-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Detail Request Bimbingan</h1>
                <a href="{{ route('dosen.request-bimbingan.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            @include('partials.alerts')

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Request</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-4">Topik/Agenda:</dt>
                                <dd class="col-sm-8">{!! nl2br(e($requestBimbingan->topik_bimbingan)) !!}</dd>

                                <dt class="col-sm-4">Jadwal Usulan:</dt>
                                <dd class="col-sm-8">
                                    {{ \Carbon\Carbon::parse($requestBimbingan->tanggal_usulan)->format('d F Y') }}
                                    pukul {{ \Carbon\Carbon::parse($requestBimbingan->jam_usulan)->format('H:i') }}
                                </dd>

                                <dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge fs-6 @switch($requestBimbingan->status_request)
                                        @case('pending') bg-warning text-dark @break
                                        @case('approved') bg-success @break
                                        @case('rejected') bg-danger @break
                                        @case('rescheduled') bg-info text-dark @break
                                        @default bg-secondary
                                    @endswitch">
                                        {{ ucfirst(str_replace('_',' ',$requestBimbingan->status_request)) }}
                                    </span>
                                </dd>

                                {{-- Menampilkan detail jika statusnya sudah final (disetujui/dijadwal ulang) --}}

                                @if($requestBimbingan->status_request == 'rescheduled')
                                <dt class="col-sm-4 text-info">Jadwal Final:</dt>
                                <dd class="col-sm-8 text-info">
                                    {{ \Carbon\Carbon::parse($requestBimbingan->tanggal_dosen)->format('d F Y') }}
                                    pukul {{ \Carbon\Carbon::parse($requestBimbingan->jam_dosen)->format('H:i') }}
                                </dd>
                                @endif

                                {{-- PENAMBAHAN: Menampilkan Lokasi Bimbingan jika sudah disetujui/dijadwal ulang --}}
                                @if(in_array($requestBimbingan->status_request, ['approved', 'rescheduled']) && $requestBimbingan->lokasi_usulan)
                                <dt class="col-sm-4">Lokasi Bimbingan:</dt>
                                <dd class="col-sm-8 fw-bold">{{ $requestBimbingan->lokasi_usulan }}</dd>
                                @endif
                                {{-- AKHIR PENAMBAHAN --}}

                                @if($requestBimbingan->catatan_dosen)
                                <dt class="col-sm-4">Catatan Dosen:</dt>
                                <dd class="col-sm-8 fst-italic">"{{ $requestBimbingan->catatan_dosen }}"</dd>
                                @endif
                            </dl>
                        </div>

                        @if($requestBimbingan->status_request == 'pending')
                        <div class="card-footer text-end">
                            <a href="{{ route('dosen.request-bimbingan.edit', $requestBimbingan->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Proses Request Ini
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary-subtle">
                            <h5 class="mb-0">Anggota Kelompok</h5>
                        </div>
                        <ul class="list-group list-group-flush">
                            @php
                                // Menggunakan approach yang aman untuk mendapatkan anggota kelompok
                                $mahasiswaPengaju = $requestBimbingan->mahasiswa;

                                // Cek apakah ada data kelompok
                                if ($mahasiswaPengaju->nomor_kelompok && $mahasiswaPengaju->prodi_id && $mahasiswaPengaju->angkatan) {
                                    // Ambil semua anggota kelompok termasuk pengaju
                                    $anggotaKelompok = \App\Models\Mahasiswa::where('nomor_kelompok', $mahasiswaPengaju->nomor_kelompok)
                                        ->where('prodi_id', $mahasiswaPengaju->prodi_id)
                                        ->where('angkatan', $mahasiswaPengaju->angkatan)
                                        ->with('user:id,name')
                                        ->get()
                                        ->sortBy('user.name');
                                } else {
                                    // Jika tidak ada kelompok, hanya tampilkan pengaju
                                    $anggotaKelompok = collect([$mahasiswaPengaju->load('user:id,name')]);
                                }
                            @endphp

                            @forelse($anggotaKelompok as $anggota)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $anggota->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $anggota->nim }}</small>
                                    </div>
                                    @if($anggota->id == $mahasiswaPengaju->id)
                                        <span class="badge bg-primary rounded-pill">Pengaju</span>
                                    @endif
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">
                                    <small>Data anggota kelompok tidak tersedia</small>
                                </li>
                            @endforelse
                        </ul>

                        @if($anggotaKelompok->count() > 1)
                        <div class="card-footer text-center">
                            <small class="text-muted">
                                Total: {{ $anggotaKelompok->count() }} anggota kelompok
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
