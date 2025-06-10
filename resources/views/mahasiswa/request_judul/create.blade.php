@extends('layouts.app')

@section('title', 'Ajukan Judul Proyek Akhir Baru')

@section('content')
<div class="container py-4 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Formulir Pengajuan Judul</h1>
                <a href="{{ route('mahasiswa.request-judul.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            @include('partials.alerts')

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('mahasiswa.request-judul.store') }}" method="POST">
                        @csrf
                        <div class="alert alert-info" role="alert">
                            <h5 class="alert-heading fw-bold"><i class="fas fa-users me-2"></i>Pengajuan Kelompok</h5>
                            <p>Judul yang Anda ajukan akan berlaku untuk seluruh anggota kelompok:</p>
                            <hr>
                            <ul class="list-unstyled mb-0">
                                @forelse ($anggotaKelompok as $anggota)
                                    <li><i class="fas fa-user-check me-2 text-success"></i><strong>{{ $anggota->user->name }}</strong> ({{ $anggota->nim }})</li>
                                @empty
                                    <li class="fst-italic"><i class="fas fa-info-circle me-2"></i>Pengajuan individu.</li>
                                @endforelse
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="judul_diajukan" class="form-label">Judul yang Diajukan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul_diajukan') is-invalid @enderror" id="judul_diajukan" name="judul_diajukan" value="{{ old('judul_diajukan') }}" required minlength="10" maxlength="255">
                            @error('judul_diajukan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Singkat / Latar Belakang<span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" required minlength="20">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="dosen_tujuan_id" class="form-label">Pilih Dosen Tujuan <span class="text-danger">*</span></label>
                            <select class="form-select @error('dosen_tujuan_id') is-invalid @enderror" id="dosen_tujuan_id" name="dosen_tujuan_id" required>
                                <option value="" disabled selected>-- Pilih Dosen dari Prodi Anda --</option>
                                @foreach ($dosenList as $dosen)
                                    <option value="{{ $dosen->id }}" {{ old('dosen_tujuan_id') == $dosen->id ? 'selected' : '' }}>
                                        {{ $dosen->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_tujuan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Kirim Pengajuan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
