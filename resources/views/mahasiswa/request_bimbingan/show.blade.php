@extends('layouts.app')

@section('title', 'Detail Pengajuan Bimbingan')

@section('content')
<div class="container py-4 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Detail Pengajuan Bimbingan</h1>
                <a href="{{ route('mahasiswa.request-bimbingan.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
            </div>
            @include('partials.alerts')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary-subtle text-primary-emphasis">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Peserta Bimbingan</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @php
                            $anggotaKelompok = $requestBimbingan->mahasiswa->semuaAnggotaKelompok()->load('user:id,name');
                        @endphp
                        @forelse ($anggotaKelompok as $anggota)
                            <li class="list-group-item">
                                <strong>{{ $anggota->user->name }}</strong> ({{ $anggota->nim }})
                                @if($anggota->id === $requestBimbingan->mahasiswa_id)
                                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill ms-1">Pengaju</span>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item"><strong>{{ $requestBimbingan->mahasiswa->user->name }}</strong> ({{ $requestBimbingan->mahasiswa->nim }}) <span class="fst-italic text-muted">- Bimbingan Individu</span></li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Informasi Pengajuan</h5></div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Status Pengajuan</dt>
                        <dd class="col-sm-8"><span class="badge fs-6 rounded-pill @switch($requestBimbingan->status_request) @case('pending') bg-warning text-dark @break @case('approved') bg-success-subtle text-success-emphasis @break @case('rejected') bg-danger-subtle text-danger-emphasis @break @case('rescheduled') bg-info-subtle text-info-emphasis @break @default bg-secondary-subtle text-secondary-emphasis @endswitch">{{ ucfirst(str_replace('_', ' ', $requestBimbingan->status_request)) }}</span></dd>
                        <dt class="col-sm-4">Dosen Pembimbing</dt>
                        <dd class="col-sm-8">{{ $requestBimbingan->dosen->user->name ?? 'N/A' }}</dd>
                        <dt class="col-sm-4">Usulan Jadwal</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($requestBimbingan->tanggal_usulan)->format('d F Y') }} pukul {{ \Carbon\Carbon::parse($requestBimbingan->jam_usulan)->format('H:i') }}</dd>
                        <dt class="col-sm-4">Topik/Agenda</dt>
                        <dd class="col-sm-8">{!! nl2br(e($requestBimbingan->topik_bimbingan)) !!}</dd>
                        @if($requestBimbingan->lokasi_usulan)<dt class="col-sm-4">Lokasi Usulan</dt><dd class="col-sm-8">{{ $requestBimbingan->lokasi_usulan }}</dd>@endif
                        @if($requestBimbingan->catatan_mahasiswa)<dt class="col-sm-4">Catatan Mahasiswa</dt><dd class="col-sm-8">{!! nl2br(e($requestBimbingan->catatan_mahasiswa)) !!}</dd>@endif
                        @if(in_array($requestBimbingan->status_request, ['rejected', 'rescheduled']) && $requestBimbingan->catatan_dosen)
                            <hr class="my-3">
                            <dt class="col-sm-4 text-{{ $requestBimbingan->status_request == 'rejected' ? 'danger' : 'info' }}">Catatan Dosen</dt>
                            <dd class="col-sm-8 text-{{ $requestBimbingan->status_request == 'rejected' ? 'danger' : 'info' }}">{!! nl2br(e($requestBimbingan->catatan_dosen)) !!}</dd>
                        @endif
                        @if($requestBimbingan->status_request == 'rescheduled')
                            <dt class="col-sm-4 text-info">Jadwal Pengganti</dt>
                            <dd class="col-sm-8 text-info">@if($requestBimbingan->tanggal_dosen && $requestBimbingan->jam_dosen){{ \Carbon\Carbon::parse($requestBimbingan->tanggal_dosen)->format('d F Y') }} pukul {{ \Carbon\Carbon::parse($requestBimbingan->jam_dosen)->format('H:i') }}@else Menunggu konfirmasi @endif</dd>
                        @endif
                    </dl>
                </div>
                @if($requestBimbingan->status_request == 'pending' && $requestBimbingan->mahasiswa->user_id === Auth::id())
                <div class="card-footer text-end">
                    <a href="{{ route('mahasiswa.request-bimbingan.edit', $requestBimbingan->id) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Edit Pengajuan</a>
                    <form action="{{ route('mahasiswa.request-bimbingan.destroy', $requestBimbingan->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Yakin batalkan pengajuan ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fas fa-times-circle me-1"></i> Batalkan Pengajuan</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
