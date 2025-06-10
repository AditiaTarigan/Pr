@extends('layouts.app')

@section('title', 'Request Bimbingan Mahasiswa')

@section('content')
<div class="container py-4 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Request Bimbingan dari Mahasiswa</h1>
    </div>
    @include('partials.alerts')
    <form method="GET" action="{{ route('dosen.request-bimbingan.index') }}" class="mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="" {{ is_null($filterStatus) ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ $filterStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $filterStatus == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $filterStatus == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="rescheduled" {{ $filterStatus == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
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
                        <thead><tr><th>No.</th><th>Pengaju (Kelompok)</th><th>Usulan Jadwal</th><th>Topik</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @foreach ($requests as $index => $request)
                                <tr>
                                    <th scope="row">{{ $requests->firstItem() + $index }}.</th>
                                    <td>
                                        <strong>Kelompok {{ $request->mahasiswa->user->name ?? 'N/A' }}</strong>
                                        <div class="mt-1">
                                            <small class="text-muted">Anggota:</small><br>
                                            <small>
                                                <!-- Pengaju Request -->
                                                <span class="badge bg-primary me-1 mb-1">{{ $request->mahasiswa->user->name ?? 'N/A' }} (Pengaju)</span><br>

                                                <!-- Anggota Kelompok Lainnya -->
                                                @if($request->mahasiswa->temanSeKelompok && $request->mahasiswa->temanSeKelompok->count() > 0)
                                                    @foreach($request->mahasiswa->temanSeKelompok as $anggota)
                                                        <span class="badge bg-secondary me-1 mb-1">{{ $anggota->user->name ?? 'N/A' }}</span>
                                                    @endforeach
                                                @endif
                                            </small>
                                        </div>
                                        <small class="d-block text-muted mt-1">({{ ($request->mahasiswa->temanSeKelompok->count() ?? 0) + 1 }} Anggota)</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($request->tanggal_usulan)->format('d M Y') }}<br><small class="text-muted">{{ \Carbon\Carbon::parse($request->jam_usulan)->format('H:i') }}</small></td>
                                    <td>{{ Str::limit($request->topik_bimbingan, 40) }}</td>
                                    <td><span class="badge @switch($request->status_request) @case('pending') bg-warning text-dark @break @case('approved') bg-success @break @case('rejected') bg-danger @break @case('rescheduled') bg-info text-dark @break @default bg-secondary @endswitch">{{ ucfirst(str_replace('_',' ',$request->status_request)) }}</span></td>
                                    <td>
                                        <a href="{{ route('dosen.request-bimbingan.show', $request->id) }}" class="btn btn-info btn-sm my-1" title="Detail"><i class="fas fa-eye"></i></a>
                                        @if($request->status_request == 'pending')
                                        <a href="{{ route('dosen.request-bimbingan.edit', $request->id) }}" class="btn btn-warning btn-sm my-1" title="Proses"><i class="fas fa-edit"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $requests->appends(request()->query())->links() }}</div>
            @else
                <div class="alert alert-info text-center">Tidak ada request bimbingan dengan filter saat ini.</div>
            @endif
        </div>
    </div>
</div>
@endsection
