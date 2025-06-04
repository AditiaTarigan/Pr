@extends('layouts.app') {{-- Atau layout dosen --}}

@section('title', 'Review Dokumen Mahasiswa')

@section('content')
<div class="container py-4 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0 h3">Dokumen Mahasiswa untuk Direview</h1>
        {{-- Tombol kembali ke dashboard bisa ditambahkan di sini jika diperlukan, atau dihilangkan sesuai referensi --}}
        {{-- <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a> --}}
    </div>

    @include('partials.alerts')

    {{-- Form Filter Status (Opsional, sesuaikan dengan kebutuhan) --}}
    {{-- Anda perlu memastikan variabel $filterStatus ada dari controller --}}
    <form method="GET" action="{{ route('dosen.review-dokumen.index') }}" class="mb-3"> {{-- Ganti 'dosen.review-dokumen.index' dengan route yang benar jika beda --}}
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="status_review" id="status_filter" class="form-select form-select-sm">
                    <option value="pending" {{ ($filterStatus ?? '') == 'pending' ? 'selected' : '' }}>Pending Review</option>
                    <option value="revision_needed" {{ ($filterStatus ?? '') == 'revision_needed' ? 'selected' : '' }}>Revision Needed</option>
                    <option value="approved" {{ ($filterStatus ?? '') == 'approved' ? 'selected' : '' }}>Approved</option> {{-- Tambahan jika ada status ini --}}
                    <option value="rejected" {{ ($filterStatus ?? '') == 'rejected' ? 'selected' : '' }}>Rejected</option> {{-- Tambahan jika ada status ini --}}
                    <option value="" {{ ($filterStatus ?? '') == '' ? 'selected' : '' }}>Semua Status</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Ganti $dokumensPending menjadi $dokumens jika Anda memfilter lebih dari sekadar pending --}}
            @if($dokumens->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Nama Mahasiswa</th>
                                <th scope="col">NIM</th>
                                <th scope="col">Jenis Dokumen</th>
                                <th scope="col">File</th>
                                <th scope="col">Versi</th>
                                <th scope="col">Tgl Submit/Update</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Ganti $dokumensPending menjadi $dokumens jika Anda memfilter lebih dari sekadar pending --}}
                            @foreach($dokumens as $index => $dokumen)
                                <tr>
                                    <th scope="row">{{ $dokumens->firstItem() + $index }}.</th>
                                    <td>{{ $dokumen->mahasiswa->user->name ?? 'N/A' }}</td>
                                    <td>{{ $dokumen->mahasiswa->nim ?? 'N/A' }}</td>
                                    <td>{{ $dokumen->jenisDokumen->nama_jenis ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank" title="Lihat/Unduh {{ $dokumen->nama_file_asli }}">
                                            <i class="fas fa-file-alt me-1"></i> {{ Str::limit($dokumen->nama_file_asli, 25) }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $dokumen->versi }}</td>
                                    <td>{{ $dokumen->updated_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        <span class="badge rounded-pill
                                            @switch($dokumen->status_review)
                                                @case('pending') bg-warning text-dark @break
                                                @case('revision_needed') bg-danger @break
                                                @case('approved') bg-success @break {{-- Sesuaikan jika ada status 'approved' --}}
                                                @case('rejected') bg-info @break   {{-- Sesuaikan jika ada status 'rejected' --}}
                                                @default bg-secondary @break
                                            @endswitch
                                        ">
                                            {{ Str::title(str_replace('_', ' ', $dokumen->status_review)) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{-- Tombol Proses akan selalu ada jika dokumen ada di list ini --}}
                                        {{-- Jika Anda ingin kondisional, tambahkan @if --}}
                                        <a href="{{ route('dosen.review-dokumen.proses', $dokumen->id) }}" class="btn btn-primary btn-sm" title="Proses Review Dokumen">
                                            <i class="fas fa-search-plus me-1"></i> Proses Review
                                        </a>
                                        {{-- Contoh tombol Detail jika ada route terpisah untuk melihat detail (mirip referensi)
                                        <a href="{{ route('dosen.review-dokumen.show', $dokumen->id) }}" class="btn btn-info btn-sm mb-1" title="Lihat Detail">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        @if(in_array($dokumen->status_review, ['pending', 'revision_needed']))
                                        <a href="{{ route('dosen.review-dokumen.proses', $dokumen->id) }}" class="btn btn-warning btn-sm mb-1" title="Proses Review">
                                            <i class="fas fa-edit"></i> Proses
                                        </a>
                                        @endif
                                        --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Ganti $dokumensPending menjadi $dokumens jika Anda memfilter lebih dari sekadar pending --}}
                @if($dokumens->hasPages())
                <div class="mt-3">
                    {{ $dokumens->appends(request()->query())->links() }} {{-- Penting untuk pagination dengan filter --}}
                </div>
                @endif
            @else
                <div class="alert alert-info text-center mb-0">
                    Tidak ada dokumen yang menunggu review dengan filter saat ini.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
