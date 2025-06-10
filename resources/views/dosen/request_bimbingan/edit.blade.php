@extends('layouts.app')

@section('title', 'Proses Request Bimbingan')

@section('content')
<div class="container py-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Proses Request Bimbingan</h1>
                <a href="{{ route('dosen.request-bimbingan.show', $requestBimbingan->id) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
            </div>
            @include('partials.alerts')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary-subtle"><h5 class="mb-0">Anggota Kelompok</h5></div>
                <ul class="list-group list-group-flush">
                    <!-- Pengaju (selalu ditampilkan) -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{$requestBimbingan->mahasiswa->user->name ?? 'N/A'}} ({{$requestBimbingan->mahasiswa->nim ?? 'N/A'}})</span>
                        <span class="badge bg-success">Pengaju</span>
                    </li>

                    <!-- Anggota kelompok lainnya -->
                    @if($requestBimbingan->mahasiswa->temanSeKelompok && $requestBimbingan->mahasiswa->temanSeKelompok->count() > 0)
                        @foreach($requestBimbingan->mahasiswa->temanSeKelompok->sortBy('user.name') as $anggota)
                        <li class="list-group-item">
                            <span>{{$anggota->user->name ?? 'N/A'}} ({{$anggota->nim ?? 'N/A'}})</span>
                        </li>
                        @endforeach
                    @endif
                </ul>
                <div class="card-footer text-muted">
                    <small>Total: {{ ($requestBimbingan->mahasiswa->temanSeKelompok ? $requestBimbingan->mahasiswa->temanSeKelompok->count() : 0) + 1 }} anggota kelompok</small>
                </div>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">Detail Request</h5></div>
                <div class="card-body">
                    <p><strong>Topik Usulan:</strong> {{ $requestBimbingan->topik_bimbingan }}</p>
                    <p><strong>Jadwal Usulan:</strong> {{ \Carbon\Carbon::parse($requestBimbingan->tanggal_usulan)->format('d F Y') }} pukul {{ \Carbon\Carbon::parse($requestBimbingan->jam_usulan)->format('H:i') }}</p>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0">Formulir Respons Anda</h5></div>
                <div class="card-body">
                    <form action="{{ route('dosen.request-bimbingan.update', $requestBimbingan->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label for="status_request" class="form-label">Tindakan <span class="text-danger">*</span></label>
                            <select class="form-select @error('status_request') is-invalid @enderror" id="status_request" name="status_request" required>
                                <option value="">-- Pilih Tindakan --</option>
                                <option value="approved">Setujui Usulan Mahasiswa</option>
                                <option value="rejected">Tolak</option>
                                <option value="rescheduled">Setujui dengan Jadwal Berbeda (Reschedule)</option>
                            </select>
                            @error('status_request')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div id="reschedule_fields" class="row mb-3" style="display: none;">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="tanggal_dosen" class="form-label">Tanggal Pengganti</label>
                                <input type="date" class="form-control @error('tanggal_dosen') is-invalid @enderror" id="tanggal_dosen" name="tanggal_dosen" value="{{ old('tanggal_dosen') }}">
                                @error('tanggal_dosen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="jam_dosen" class="form-label">Jam Pengganti</label>
                                <input type="time" class="form-control @error('jam_dosen') is-invalid @enderror" id="jam_dosen" name="jam_dosen" value="{{ old('jam_dosen') }}">
                                @error('jam_dosen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="catatan_dosen" class="form-label">Catatan (Wajib jika menolak/reschedule)</label>
                            <textarea class="form-control @error('catatan_dosen') is-invalid @enderror" id="catatan_dosen" name="catatan_dosen" rows="5" placeholder="Berikan alasan atau informasi tambahan...">{{ old('catatan_dosen') }}</textarea>
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
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelect = document.getElementById('status_request');
        const rescheduleFields = document.getElementById('reschedule_fields');
        const tanggalDosenInput = document.getElementById('tanggal_dosen');
        const jamDosenInput = document.getElementById('jam_dosen');
        function toggleRescheduleFields() {
            if (statusSelect.value === 'rescheduled') {
                rescheduleFields.style.display = 'flex';
                tanggalDosenInput.required = true; jamDosenInput.required = true;
            } else {
                rescheduleFields.style.display = 'none';
                tanggalDosenInput.required = false; jamDosenInput.required = false;
            }
        }
        if(statusSelect) { statusSelect.addEventListener('change', toggleRescheduleFields); }
    });
</script>
@endpush
