@extends('layouts.app')

@section('title', 'Detail Pengajuan Judul Mahasiswa')

@section('content')
<div class="container py-4 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Detail Pengajuan Judul</h1>
        <a href="{{ route('dosen.request-judul.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
    @include('partials.alerts')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">Informasi Pengajuan</h5></div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Judul Diajukan:</dt><dd class="col-sm-8 lead">{{ $requestJudul->judul_diajukan }}</dd>
                        <dt class="col-sm-4">Deskripsi:</dt><dd class="col-sm-8">{!! nl2br(e($requestJudul->deskripsi)) !!}</dd>
                        <dt class="col-sm-4">Tanggal Pengajuan:</dt><dd class="col-sm-8">{{ $requestJudul->created_at->format('d M Y, H:i') }}</dd>
                        <dt class="col-sm-4">Status:</dt><dd class="col-sm-8"><span class="badge fs-6 @switch($requestJudul->status) @case('pending') bg-warning text-dark @break @case('approved') bg-success @break @case('rejected') bg-danger @break @default bg-secondary @endswitch">{{ ucfirst($requestJudul->status) }}</span></dd>
                        @if($requestJudul->catatan_dosen)
                        <dt class="col-sm-4">Catatan Dosen:</dt><dd class="col-sm-8 fst-italic">"{{ $requestJudul->catatan_dosen }}"</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary-subtle"><h5 class="mb-0">Anggota Kelompok</h5></div>
                <ul class="list-group list-group-flush">
                    @php $anggotaKelompok = $requestJudul->mahasiswa->temanSeKelompok->push($requestJudul->mahasiswa); @endphp
                    @foreach($anggotaKelompok->sortBy('user.name') as $anggota)
                    <li class="list-group-item">{{$anggota->user->name}} ({{$anggota->nim}})</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
