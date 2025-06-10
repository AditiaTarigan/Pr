@extends('layouts.app')

@section('title', 'Daftar Pengajuan Bimbingan')

@section('content')
<div class="container py-4 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Daftar Pengajuan Bimbingan Kelompok</h1>
        @if($hasDosbing)
            <a href="{{ route('mahasiswa.request-bimbingan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Ajukan Bimbingan Baru
            </a>
        @endif
    </div>

    @include('partials.alerts')

    @if(!$hasDosbing)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>Kelompok Anda belum memiliki dosen pembimbing. Anda baru bisa mengajukan bimbingan setelah judul disetujui dan dosen pembimbing ditetapkan.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">Histori Pengajuan Bimbingan</div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Dosen Pembimbing</th>
                                <th scope="col">Tgl & Jam Usulan</th>
                                <th scope="col">Topik</th>
                                <th scope="col">Diajukan Oleh</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $index => $request)
                                <tr>
                                    <th scope="row">{{ $requests->firstItem() + $index }}</th>
                                    <td>{{ $request->dosen->user->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($request->tanggal_usulan)->format('d M Y') }}
                                        <br>
                                        <small class="text-muted"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($request->jam_usulan)->format('H:i') }}</small>
                                    </td>
                                    <td>{{ Str::limit($request->topik_bimbingan, 40) }}</td>
                                    <td>
                                        {{ Str::words($request->mahasiswa->user->name, 2, '') }}
                                        @if($request->mahasiswa->user_id === Auth::id())
                                            <span class="badge bg-info-subtle text-info-emphasis rounded-pill ms-1">Anda</span>
                                        @endif
                                    </td>
                                    <td><span class="badge rounded-pill @switch($request->status_request) @case('pending') bg-warning text-dark @break @case('approved') bg-success-subtle text-success-emphasis @break @case('rejected') bg-danger-subtle text-danger-emphasis @break @case('rescheduled') bg-info-subtle text-info-emphasis @break @default bg-secondary-subtle text-secondary-emphasis @endswitch">{{ ucfirst(str_replace('_', ' ', $request->status_request)) }}</span></td>
                                    <td>
                                        <a href="{{ route('mahasiswa.request-bimbingan.show', $request->id) }}" class="btn btn-info btn-sm my-1" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                        @if($request->status_request == 'pending' && $request->mahasiswa->user_id === Auth::id())
                                            <a href="{{ route('mahasiswa.request-bimbingan.edit', $request->id) }}" class="btn btn-warning btn-sm my-1" title="Edit"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('mahasiswa.request-bimbingan.destroy', $request->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin batalkan pengajuan ini?');">
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
            @elseif($hasDosbing)
                <div class="alert alert-secondary text-center">
                    <i class="fas fa-info-circle"></i> Belum ada pengajuan bimbingan dari kelompok Anda.
                    <a href="{{ route('mahasiswa.request-bimbingan.create') }}" class="btn btn-link p-0 align-baseline">Ajukan sekarang?</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
