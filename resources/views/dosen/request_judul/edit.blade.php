@extends('layouts.app')

@section('title', 'Proses Pengajuan Judul')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Proses Pengajuan Judul</h1>
                <a href="{{ route('dosen.request-judul.show', $requestJudul->id) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
            </div>
            @include('partials.alerts')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary-subtle"><h5 class="mb-0">Anggota Kelompok</h5></div>
                <ul class="list-group list-group-flush">
                    @php $anggotaKelompok = $requestJudul->mahasiswa->temanSeKelompok->push($requestJudul->mahasiswa); @endphp
                    @foreach($anggotaKelompok->sortBy('user.name') as $anggota)
                    <li class="list-group-item">{{$anggota->user->name}} ({{$anggota->nim}})</li>
                    @endforeach
                </ul>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">Detail Pengajuan</h5></div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Judul Diajukan</dt><dd class="col-sm-9">{{ $requestJudul->judul_diajukan }}</dd>
                        <dt class="col-sm-3">Deskripsi</dt><dd class="col-sm-9">{!! nl2br(e($requestJudul->deskripsi)) !!}</dd>
                    </dl>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0">Formulir Respons Dosen</h5></div>
                <div class="card-body">
                    <form action="{{ route('dosen.request-judul.update', $requestJudul->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label for="status" class="form-label">Tindakan <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="" disabled selected>-- Pilih Tindakan --</option>
                                <option value="approved">Setujui (Approved)</option>
                                <option value="rejected">Tolak (Rejected)</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="catatan_dosen" class="form-label">Catatan (Opsional jika disetujui, Wajib jika ditolak)</label>
                            <textarea class="form-control @error('catatan_dosen') is-invalid @enderror" id="catatan_dosen" name="catatan_dosen" rows="5" placeholder="Berikan alasan atau masukan...">{{ old('catatan_dosen') }}</textarea>
                            @error('catatan_dosen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <hr>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Respons</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
