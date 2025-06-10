@extends('layouts.app')

@section('title', 'Detail Pengajuan Judul')

@section('content')
<div class="container py-4 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Detail Pengajuan Judul</h1>
                <a href="{{ route('mahasiswa.request-judul.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
            </div>
            @include('partials.alerts')

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary-subtle text-primary-emphasis">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Pengaju Judul (Kelompok)</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @php
                            $anggotaKelompok = $requestJudul->mahasiswa->semuaAnggotaKelompok()->load('user:id,name');
                        @endphp
                        @forelse ($anggotaKelompok as $anggota)
                            <li class="list-group-item">
                                <strong>{{ $anggota->user->name }}</strong> ({{ $anggota->nim }})
                                @if($anggota->id === $requestJudul->mahasiswa_id)
                                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill ms-1">Pengaju</span>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item"><strong>{{ $requestJudul->mahasiswa->user->name }}</strong> ({{ $requestJudul->mahasiswa->nim }}) <span class="fst-italic text-muted">- Pengajuan Individu</span></li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Informasi Pengajuan</h5></div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8"><span class="badge fs-6 rounded-pill @switch($requestJudul->status) @case('pending') bg-warning text-dark @break @case('approved') bg-success-subtle text-success-emphasis @break @case('rejected') bg-danger-subtle text-danger-emphasis @break @default bg-secondary-subtle @endswitch">{{ ucfirst($requestJudul->status) }}</span></dd>
                        <dt class="col-sm-4">Judul Diajukan</dt>
                        <dd class="col-sm-8 fw-bold">{{ $requestJudul->judul_diajukan }}</dd>
                        <dt class="col-sm-4">Dosen Tujuan</dt>
                        <dd class="col-sm-8">{{ $requestJudul->dosenTujuan->user->name ?? 'N/A' }}</dd>
                        <dt class="col-sm-4">Deskripsi</dt>
                        <dd class="col-sm-8">{!! nl2br(e($requestJudul->deskripsi)) !!}</dd>
                        <dt class="col-sm-4">Tanggal Diajukan</dt>
                        <dd class="col-sm-8">{{ $requestJudul->created_at->format('d F Y, H:i') }}</dd>
                        @if($requestJudul->status == 'rejected' && $requestJudul->catatan_dosen)
                            <hr class="my-3">
                            <dt class="col-sm-4 text-danger">Catatan Dosen</dt>
                            <dd class="col-sm-8 text-danger">{!! nl2br(e($requestJudul->catatan_dosen)) !!}</dd>
                        @endif
                    </dl>
                </div>
                @if($requestJudul->status == 'pending' && $requestJudul->mahasiswa->user_id === Auth::id())
                <div class="card-footer text-end">
                    <a href="{{ route('mahasiswa.request-judul.edit', $requestJudul->id) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Edit</a>
                    <form action="{{ route('mahasiswa.request-judul.destroy', $requestJudul->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Yakin batalkan pengajuan ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fas fa-times-circle me-1"></i> Batalkan</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
