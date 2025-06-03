@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> {{-- CSS utama Anda --}}
    {{-- <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css' rel='stylesheet' /> --}} {{-- Anda bisa hapus ini jika tidak menggunakan FullCalendar --}}
@endpush


@section('content')
<div class="dashboard-container">

    <div class="main-content">

        {{-- Baris 1: Welcome Card & Status Proyek Akhir --}}
        <div class="row mb-2">
            {{-- Kolom Kiri: Welcome Card --}}
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card welcome-card shadow-sm h-100">
                    <div class="card-body d-flex">
                    <img src="{{ asset('foto/biodata.PNG') }}" class="baground-light me-3" style="width: 150px; height: auto;">
                        <div>
                            <h2><b>Selamat Datang, SIPA Vokasi <br>IT DEL, {{ Auth::user()->name ?? 'Pengguna' }}!</b></h2>
                            <p class="text-muted">Data Anda telah terverifikasi:</p>
                            {{-- Pastikan $mahasiswa dikirim dari DashboardController --}}
                            <p>Nama :{{ $mahasiswa->user->name ?? Auth::user()->name ?? 'Pengguna' }}
                            <br>NIM :{{ $mahasiswa->nim ?? 'N/A' }}
                            <br>Prodi: {{ $mahasiswa->prodi->nama_prodi ?? 'N/A' }}</p>
                            <p class="text-muted">Manfaatkan SIPA untuk pengerjaan PA yang lebih terorganisir.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Status Proyek Akhir --}}
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Status Proyek Akhir Anda</h5>
                    </div>
                    <div class="card-body">
                        {{-- Pastikan $mahasiswa dikirim dari DashboardController --}}
                        @if($mahasiswa && $mahasiswa->judul_proyek_akhir)
                            <h6 class="card-title">Judul: {{ $mahasiswa->judul_proyek_akhir }}</h6>
                            <p class="card-text">
                                Status Saat Ini:
                                <span class="badge
                                    @switch($mahasiswa->status_proyek_akhir)
                                        @case('belum_ada') bg-secondary @break
                                        @case('pengajuan_judul') bg-info text-dark @break
                                        @case('bimbingan') bg-primary @break
                                        @case('selesai') bg-success @break
                                        @case('revisi') bg-warning text-dark @break
                                        @default bg-light text-dark @break
                                    @endswitch
                                ">
                                    {{ ucwords(str_replace('_', ' ', $mahasiswa->status_proyek_akhir)) }}
                                </span>
                            </p>
                            <p>Dosen Pembimbing: {{ $mahasiswa->dosenPembimbing->user->name ?? 'Belum ditentukan' }}</p>
                        @else
                            <p class="text-muted">Anda belum mengajukan judul proyek akhir atau status belum tersedia.</p>
                            <a href="{{ route('mahasiswa.request-judul.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus-circle me-1"></i> Ajukan Judul Sekarang</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Baris 2: Aksi Cepat (Kiri) & Kalender/Pengumuman (Kanan) --}}
        <div class="row">
            {{-- Kolom Kiri (lebih besar): Aksi Cepat --}}
            <div class="col-md-8">
                {{-- Baris Aksi Cepat 1 --}}
                <div class="row mb-4">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <a href="{{ route('mahasiswa.request-judul.index') }}" class="text-decoration-none text-dark">
                            <div class="card shadow-sm h-100"> <div class="card-body d-flex align-items-center p-3"> <div class="flex-shrink-0 me-3"> <i class="fas fa-lightbulb fa-2x text-warning"></i> </div> <div class="flex-grow-1"> <h4>Pengajuan Judul</h4> <p class="small text-muted mb-0">Mahasiswa mengusulkan judul proyek akhir</p> </div> </div> </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('mahasiswa.request-bimbingan.index') }}" class="text-decoration-none text-dark">
                            <div class="card shadow-sm h-100"> <div class="card-body d-flex align-items-center p-3"> <div class="flex-shrink-0 me-3"> <i class="fas fa-calendar-plus fa-2x text-info"></i> </div> <div class="flex-grow-1"> <h4>Pengajuan Bimbingan</h4> <p class="small text-muted mb-0">Ajukan sesi bimbingan dengan jadwal dan topic yang dibahas</p> </div> </div> </div>
                        </a>
                    </div>
                </div>

                {{-- Baris Aksi Cepat 2 --}}
                <div class="row mb-4">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <a href="{{ route('mahasiswa.dokumen.index') }}" class="text-decoration-none text-dark">
                            <div class="card shadow-sm h-100"> <div class="card-body d-flex align-items-center p-3"> <div class="flex-shrink-0 me-3"> <i class="fas fa-folder-open fa-2x text-primary"></i> </div> <div class="flex-grow-1"> <h4>Dokumen Proyek Akhir</h4> <p class="small text-muted mb-0">Membantu mendokumentasikan setiap proses pengembangan Proyek Akhir</p> </div> </div> </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('mahasiswa.history-bimbingan.index') }}" class="text-decoration-none text-dark">
                            <div class="card shadow-sm h-100"> <div class="card-body d-flex align-items-center p-3"> <div class="flex-shrink-0 me-3"> <i class="fas fa-history fa-2x text-success"></i> </div> <div class="flex-grow-1"> <h4>Riwayat Bimbingan</h4> <p class="small text-muted mb-0">Membantu mencatat setiap proses bimbingan Proyek Akhir sebelumnya</p> </div> </div> </div>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan (lebih kecil): Kalender & Pengumuman --}}
            <div class="col-md-4">
                {{-- AWAL MODIFIKASI KALENDER --}}
                @if(isset($cal_currentMonthDateObject) && isset($cal_days) && isset($today) && isset($events)) {{-- Pastikan semua variabel kalender ada --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('dashboard', $cal_previousMonthLinkParams ?? []) }}" class="btn btn-outline-primary btn-sm py-0 px-1">< Prev</a>
                            <h6 class="mb-0 small-calendar-header">{{ $cal_monthName ?? 'Bulan' }} {{ $cal_year ?? 'Tahun' }}</h6>
                            <a href="{{ route('dashboard', $cal_nextMonthLinkParams ?? []) }}" class="btn btn-outline-primary btn-sm py-0 px-1">Next ></a>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-bordered text-center small-calendar"> {{-- Menggunakan class small-calendar dari CSS Anda --}}
                            <thead>
                                <tr>
                                    {{-- Header hari S,S,R,K,J,S,M dari gambar Anda --}}
                                    @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
                                    <th scope="col">{{ substr($dayName, 0, 1) }}</th> {{-- Ambil huruf pertama saja --}}
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                @php $dayCounter = 0; @endphp
                                @foreach ($cal_days as $day)
                                    @if($loop->first && $day->dayOfWeekIso > 1)
                                        <td colspan="{{ $day->dayOfWeekIso - 1 }}" class="other-month"></td>
                                        @php $dayCounter += ($day->dayOfWeekIso - 1); @endphp
                                    @endif

                                    {{-- Logika untuk class <td> dan event --}}
                                    <td class="
                                        @php
                                            $cellClasses = [];
                                            $dateString = $day->toDateString();
                                            $dailyEvents = (isset($events) && is_array($events) && isset($events[$dateString])) ? $events[$dateString] : [];

                                            if ($day->month != $cal_currentMonthDateObject->month) { $cellClasses[] = 'other-month'; }

                                            $hasApprovedEventOnDay = false; $hasRescheduledEventOnDay = false;
                                            if (!empty($dailyEvents)) {
                                                foreach ($dailyEvents as $eventItem) {
                                                    if (isset($eventItem['status'])) {
                                                        if ($eventItem['status'] == 'approved') $hasApprovedEventOnDay = true;
                                                        if ($eventItem['status'] == 'rescheduled') $hasRescheduledEventOnDay = true;
                                                    }
                                                }
                                            }

                                            // Terapkan class background berdasarkan status event
                                            if ($hasApprovedEventOnDay) { $cellClasses[] = 'cal-mhs-event-approved-bg'; }
                                            elseif ($hasRescheduledEventOnDay) { $cellClasses[] = 'cal-mhs-event-rescheduled-bg'; }

                                            // Hari ini: background biru tua jika TIDAK ada event bimbingan
                                            if ($day->isSameDay($today)) {
                                                $cellClasses[] = 'today'; // Class 'today' dari CSS Anda akan berlaku
                                                // Jika CSS Anda .today sudah biru dan teks putih, tidak perlu class tambahan
                                                // Jika tidak, dan HANYA jika tidak ada event bimbingan:
                                                // if (!$hasApprovedEventOnDay && !$hasRescheduledEventOnDay) {
                                                //    $cellClasses[] = 'cal-mhs-today-no-event-bg';
                                                // }
                                            }
                                            echo implode(' ', array_unique($cellClasses));
                                        @endphp
                                    ">
                                        <div class="day-number">{{ $day->day }}</div>
                                        @if(!empty($dailyEvents))
                                            @foreach($dailyEvents as $event)
                                                @php
                                                    $indicatorClass = 'cal-mhs-event-default-indicator';
                                                    $eventTitle = $event['title'] ?? 'Bimbingan';
                                                    $tooltipTitle = "Bimbingan: " . $eventTitle;
                                                    if(isset($event['dosen_nama'])) {$tooltipTitle .= " (Dsn: " . Str::limit($event['dosen_nama'], 10) . ")";}
                                                    if(isset($event['jam'])) {$tooltipTitle .= " Jam: " . $event['jam'];}

                                                    if (isset($event['status'])) {
                                                        if ($event['status'] == 'approved') { $indicatorClass = 'cal-mhs-event-approved-indicator'; }
                                                        elseif ($event['status'] == 'rescheduled') { $indicatorClass = 'cal-mhs-event-rescheduled-indicator'; }
                                                    }
                                                    $detailRoute = '#';
                                                    if (isset($event['request_id'])) {
                                                        try { $detailRoute = route('mahasiswa.request-bimbingan.show', $event['request_id']); }
                                                        catch (\Exception $e) { $detailRoute = '#'; }
                                                    }
                                                @endphp
                                                <a href="{{ $detailRoute }}" class="event-strip-mhs {{ $indicatorClass }} rounded-pill px-1 small d-block my-0" title="{{ $tooltipTitle }}">
                                                    {{ Str::limit($eventTitle, 5) }}
                                                </a>
                                            @endforeach
                                        @endif
                                    </td>

                                    @php $dayCounter++; @endphp
                                    @if($dayCounter % 7 == 0 && !$loop->last)
                                        </tr><tr>
                                    @endif
                                @endforeach
                                @if($dayCounter % 7 != 0)
                                    <td colspan="{{ 7 - ($dayCounter % 7) }}" class="other-month"></td>
                                @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- Legenda bisa ditambahkan di footer card jika diinginkan --}}
                    <div class="card-footer bg-white border-0 pt-0 pb-2 px-2 text-center small">
                        <span class="me-2"><span class="cal-mhs-legend-dot cal-mhs-event-approved-indicator"></span> Apprv</span>
                        <span><span class="cal-mhs-legend-dot cal-mhs-event-rescheduled-indicator"></span> Rschd</span>
                    </div>
                </div>
                @else
                <div class="card mb-4 shadow-sm">
                    <div class="card-header"><h6 class="mb-0 small-calendar-header">Kalender Bimbingan</h6></div>
                    <div class="card-body p-2"><p class="text-muted small text-center">Data kalender tidak tersedia.</p></div>
                </div>
                @endif
                {{-- AKHIR MODIFIKASI KALENDER --}}


                {{-- Pengumuman (KODE ASLI ANDA) --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Pengumuman</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($announcements) && count($announcements) > 0)
                            @foreach($announcements as $index => $announcement)
                                <div class="alert alert-info mb-2 p-2" role="alert">
                                    <h6 class="alert-heading small">{{ $announcement->title }}</h6>
                                    <p class="mb-1 x-small">{{ Str::limit($announcement->content, 70) }}</p>
                                    <p class="mb-0 xx-small"><small>Diposting: {{ $announcement->created_at->format('d M Y') }}</small></p>
                                </div>
                                @if($index >= 1) @break @endif
                            @endforeach
                        @else
                            <p class="text-muted">Tidak ada pengumuman terbaru.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    /* Penting: Pastikan nilai padding-top ini sesuai dengan tinggi navbar Anda */
    body {
        padding-top: 65px; /* CONTOH: GANTI DENGAN TINGGI NAVBAR AKTUAL */
    }
    .dashboard-container .main-content { /* padding: 1rem; */ }
    .welcome-card .avatar { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; }
    .welcome-card .welcome-text h5, .welcome-card .welcome-text h6 { margin-bottom: 0.25rem; }
    .welcome-card .card-body { align-items: center; }

    /* Styles for a SMALLER calendar in the sidebar (DARI CSS ANDA) */
    .small-calendar th, .small-calendar td {
        height: auto;
        padding: 2px !important;
        font-size: 0.75rem;
        vertical-align: top;
        border: 1px solid #eee; /* Menambahkan border tipis seperti di gambar */
    }
    .small-calendar th { /* Styling untuk header hari S,S,R.. */
        font-weight: 500;
        color: #6c757d;
    }
    .small-calendar .day-number {
        font-weight: bold;
        font-size: 0.8em;
        text-align: center;
        padding: 2px 0;
        display: block;
        line-height: 1.2;
        color: #495057; /* Warna default nomor hari */
    }
    .small-calendar .other-month .day-number { color: #ccc !important; }
    .small-calendar .other-month { background-color: #fdfdfd !important; } /* Latar other-month lebih terang */

    /* Hari ini default dari CSS Anda (biru, teks putih, border radius) */
    .small-calendar .today {
        background-color: #007bff !important;
        color: white !important;
        border-radius: 3px; /* Sedikit radius jika mau, atau 50% untuk bulat */
        font-weight: bold;
    }
    .small-calendar .today .day-number { color: white !important; }

    .small-calendar-header { font-size: 0.9rem; font-weight: 500; }
    .small-calendar .btn-sm { padding: 0.1rem 0.3rem; font-size: 0.75rem; }

    .x-small { font-size: 0.75rem; }
    .xx-small { font-size: 0.65rem; }
    .card-sidebar-kalender { min-height: 350px; }
    .card-sidebar-status { min-height: 200px; }
    .card-action-item .card-body { display: flex; align-items: center; }
    .card-action-item .flex-shrink-0 { margin-right: 1rem; }

    /* AWAL CSS BARU UNTUK EVENT DI SMALL-CALENDAR */
    .small-calendar td.cal-mhs-event-approved-bg {
        background-color: #e6f7e9 !important; /* Hijau sangat muda (sesuaikan dengan gambar) */
    }
    .small-calendar td.cal-mhs-event-approved-bg .day-number {
        color: #198754; /* Teks hijau tua */
    }
    .small-calendar td.cal-mhs-event-rescheduled-bg {
        background-color: #e0f2fe !important; /* Biru sangat muda (sesuaikan dengan gambar) */
    }
    .small-calendar td.cal-mhs-event-rescheduled-bg .day-number {
        color: #0870a1; /* Teks biru tua */
    }

    /* Strip Event (sesuaikan dengan gambar Anda) */
    .event-strip-mhs {
        font-size: 0.65em !important;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        display: block; color: white !important; text-decoration: none;
        padding: 1px 3px !important; /* Padding strip lebih kecil */
        text-align: center; line-height: 1.1; margin-top: 1px;
        border-radius: 0.75rem; /* rounded-pill */
    }
    .event-strip-mhs:hover { opacity: 0.85; text-decoration: none; color: white !important; }

    .cal-mhs-event-approved-indicator { background-color: #28a745 !important; } /* Hijau solid */
    .cal-mhs-event-rescheduled-indicator { background-color: #0dcaf0 !important; } /* Biru solid */
    .cal-mhs-event-default-indicator { background-color: #6c757d !important; } /* Abu-abu */

    /* Legenda */
    .cal-mhs-legend-dot {
        display: inline-block; width: 8px; height: 8px;
        border-radius: 50%; margin-right: 3px; vertical-align: middle;
    }
    /* AKHIR CSS BARU */
</style>
@endpush
