@extends('layouts.app')

@section('title', 'Daftar Pengajuan Judul')

@section('content')
<div class="container py-4 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Daftar Pengajuan Judul Kelompok</h1>
        @if($canRequestNewTitle)
            <a href="{{ route('mahasiswa.request-judul.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Ajukan Judul Baru
            </a>
        @endif
    </div>

    @include('partials.alerts')

    @if(!$canRequestNewTitle && $requests->isNotEmpty())
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>Kelompok Anda sudah memiliki judul yang disetujui atau sedang dalam proses bimbingan. Pengajuan judul baru ditutup.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">Histori Pengajuan Judul</div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Judul Diajukan</th>
                                <th scope="col">Dosen Tujuan</th>
                                <th scope="col">Diajukan Oleh</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $index => $request)
                                <tr>
                                    <th scope="row">{{ $requests->firstItem() + $index }}</th>
                                    <td>{{ Str::limit($request->judul_diajukan, 45) }}</td>
                                    <td>{{ $request->dosenTujuan->user->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ Str::words($request->mahasiswa->user->name, 2, '') }}
                                        @if($request->mahasiswa->user_id === Auth::id())
                                            <span class="badge bg-info-subtle text-info-emphasis rounded-pill ms-1">Anda</span>
                                        @endif
                                    </td>
                                    <td><span class="badge rounded-pill @switch($request->status) @case('pending') bg-warning text-dark @break @case('approved') bg-success-subtle text-success-emphasis @break @case('rejected') bg-danger-subtle text-danger-emphasis @break @default bg-secondary-subtle @endswitch">{{ ucfirst($request->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('mahasiswa.request-judul.show', $request->id) }}" class="btn btn-info btn-sm my-1" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                        @if($request->status == 'pending' && $request->mahasiswa->user_id === Auth::id())
                                            <a href="{{ route('mahasiswa.request-judul.edit', $request->id) }}" class="btn btn-warning btn-sm my-1" title="Edit"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('mahasiswa.request-judul.destroy', $request->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin batalkan pengajuan ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm my-1" title="Batalkan"><i class="fas fa-times-circle"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $requests->links() }}</div>
            @else
                @if($canRequestNewTitle)
                    <div class="alert alert-secondary text-center"><i class="fas fa-info-circle"></i> Belum ada pengajuan judul dari kelompok Anda.</div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
