@extends('layouts.app')

@section('title', 'Daftar Pengajuan Judul Mahasiswa')

@section('content')
<div class="container py-4 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Pengajuan Judul dari Mahasiswa</h1>
    </div>
    @include('partials.alerts')
    <form method="GET" action="{{ route('dosen.request-judul.index') }}" class="mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="status" id="status_filter" class="form-select form-select-sm">
                    <option value="" {{ is_null($filterStatus) ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ $filterStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $filterStatus == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $filterStatus == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary btn-sm w-100">Filter</button></div>
        </div>
    </form>
    <div class="card shadow-sm">
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>No.</th><th>Pengaju (Kelompok)</th><th>Prodi</th><th>Judul Diajukan</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @foreach ($requests as $index => $request)
                                <tr>
                                    <th scope="row">{{ $requests->firstItem() + $index }}.</th>
                                    <td>
                                        Kelompok {{ $request->mahasiswa->user->name ?? 'N/A' }}
                                        <small class="d-block text-muted">({{$request->mahasiswa->temanSeKelompok->count() + 1}} Anggota)</small>
                                    </td>
                                    <td>{{ $request->mahasiswa->prodi->nama_prodi ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($request->judul_diajukan, 40) }}</td>
                                    <td>{{ $request->created_at->format('d M Y') }}</td>
                                    <td><span class="badge @switch($request->status) @case('pending') bg-warning text-dark @break @case('approved') bg-success @break @case('rejected') bg-danger @break @default bg-secondary @endswitch">{{ ucfirst($request->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('dosen.request-judul.show', $request->id) }}" class="btn btn-info btn-sm mb-1" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                        @if($request->status == 'pending')
                                        <a href="{{ route('dosen.request-judul.edit', $request->id) }}" class="btn btn-warning btn-sm mb-1" title="Proses Pengajuan"><i class="fas fa-edit"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $requests->appends(request()->query())->links() }}</div>
            @else
                <div class="alert alert-info text-center">Tidak ada pengajuan judul dengan filter saat ini.</div>
            @endif
        </div>
    </div>
</div>
@endsection
