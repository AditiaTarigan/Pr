@extends('layouts.app')

@section('title', 'Tambah Dosen Baru')

@section('content')
<div class="container py-4 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header"><h1 class="mb-0 h4"><i class="fas fa-chalkboard-teacher me-2"></i>Tambah Dosen Baru</h1></div>
                <div class="card-body">
                    @include('partials.alerts')
                    <form action="{{ route('admin.dosen.store') }}" method="POST">
                        @csrf
                        <h5 class="mb-3">Informasi Akun Pengguna</h5>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Informasi Detail Dosen</h5>
                        <div class="mb-3">
                            <label for="nidn" class="form-label">NIDN/NIP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nidn') is-invalid @enderror" id="nidn" name="nidn" value="{{ old('nidn') }}" required>
                            @error('nidn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="prodi_id" class="form-label">Program Studi <span class="text-danger">*</span></label>
                            <select class="form-select @error('prodi_id') is-invalid @enderror" id="prodi_id" name="prodi_id" required>
                                <option value="" disabled {{ old('prodi_id') ? '' : 'selected' }}>Pilih Prodi...</option>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_prodi }}</option>
                                @endforeach
                            </select>
                            @error('prodi_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="spesialisasi" class="form-label">Spesialisasi/Bidang Keahlian</label>
                            <textarea class="form-control @error('spesialisasi') is-invalid @enderror" id="spesialisasi" name="spesialisasi" rows="3">{{ old('spesialisasi') }}</textarea>
                            @error('spesialisasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <a href="{{ route('admin.dosen.index') }}" class="btn btn-secondary me-2"><i class="fas fa-times me-1"></i> Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Dosen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
