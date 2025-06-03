{{-- File: resources/views/dashboards/dosen.blade.php --}}
@extends('layouts.app') {{-- Atau layout utama Anda --}}

@section('title', 'Dashboard Dosen')

@section('content')
<div class="container py-4 mt-5">
    <div class="row">
        {{-- Kolom Kiri: Kalender & Legenda --}}
        <div class="col-md-8 mb-4">
            {{-- KALENDER --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route(Route::currentRouteName(), $cal_previousMonthLinkParams ?? []) }}" class="btn btn-outline-primary btn-sm">< Prev</a>
                        <h5 class="mb-0">{{ $cal_monthName ?? 'Bulan' }} {{ $cal_year ?? 'Tahun' }}</h5>
                        <a href="{{ route(Route::currentRouteName(), $cal_nextMonthLinkParams ?? []) }}" class="btn btn-outline-primary btn-sm">Next ></a>
                    </div>
                </div>
                <div class="card-body p-2">
                    {{-- Pastikan semua variabel ini ada dari controller --}}
                    @if(isset($cal_days) && isset($cal_currentMonthDateObject) && isset($today) && isset($events))
                    <table class="table table-bordered text-center calendar-table">
                        <thead>
                            <tr>
                                {{-- Loop untuk header hari --}}
                                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
                                <th scope="col" style="width: 14.28%;">{{ $dayName }}</th>
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

                                {{-- Logika untuk class <td> --}}
                                <td class="
                                    @php
                                        $cellClasses = [];
                                        $dateString = $day->toDateString();
                                        // Ambil event untuk hari ini, pastikan $events ada dan merupakan array
                                        $dailyEvents = (isset($events) && is_array($events) && isset($events[$dateString])) ? $events[$dateString] : [];

                                        if ($day->month != $cal_currentMonthDateObject->month) {
                                            $cellClasses[] = 'other-month';
                                        }

                                        $hasApprovedEventOnDay = false;
                                        $hasRescheduledEventOnDay = false;
                                        if (!empty($dailyEvents)) {
                                            foreach ($dailyEvents as $eventItem) {
                                                if (isset($eventItem['status'])) {
                                                    if ($eventItem['status'] == 'approved') $hasApprovedEventOnDay = true;
                                                    if ($eventItem['status'] == 'rescheduled') $hasRescheduledEventOnDay = true;
                                                }
                                            }
                                        }

                                        // Terapkan class background berdasarkan status event
                                        if ($hasApprovedEventOnDay) {
                                            $cellClasses[] = 'bimbingan-approved-bg';
                                        } elseif ($hasRescheduledEventOnDay) { // Prioritaskan approved jika ada keduanya
                                            $cellClasses[] = 'bimbingan-rescheduled-bg';
                                        }

                                        // Terapkan class untuk hari ini
                                        if ($day->isSameDay($today)) {
                                            $cellClasses[] = 'today'; // Untuk border
                                            // Jika hari ini dan TIDAK ADA event bimbingan, gunakan style hari ini default
                                            if (!$hasApprovedEventOnDay && !$hasRescheduledEventOnDay) {
                                                $cellClasses[] = 'bg-primary';
                                                $cellClasses[] = 'text-white-custom';
                                            }
                                        }
                                        echo implode(' ', array_unique($cellClasses));
                                    @endphp
                                ">
                                    <div class="day-number">{{ $day->day }}</div>

                                    {{-- Tampilkan Indikator Event --}}
                                    @if(!empty($dailyEvents))
                                        @foreach($dailyEvents as $event)
                                            @php
                                                $indicatorClass = 'bg-secondary text-white'; // Default
                                                $eventTitle = $event['title'] ?? 'Bimbingan';
                                                $eventTime = isset($event['jam']) ? ' (' . $event['jam'] . ')' : '';
                                                $eventCatatan = isset($event['catatan_dosen']) && $event['catatan_dosen'] ? ' - Ket: ' . Str::limit($event['catatan_dosen'], 15) : '';

                                                if (isset($event['status'])) {
                                                    if ($event['status'] == 'approved') {
                                                        $indicatorClass = 'bimbingan-approved-indicator';
                                                    } elseif ($event['status'] == 'rescheduled') {
                                                        $indicatorClass = 'bimbingan-rescheduled-indicator';
                                                    }
                                                }

                                                // Membuat link detail request bimbingan
                                                $detailRoute = '#'; // Default fallback
                                                if (isset($event['request_id'])) {
                                                    try {
                                                        // Pastikan nama route 'dosen.request-bimbingan.show' benar
                                                        $detailRoute = route('dosen.request-bimbingan.show', $event['request_id']);
                                                    } catch (\Exception $e) {
                                                        // Opsional: Log error
                                                        // \Illuminate\Support\Facades\Log::error("Error creating route 'dosen.request-bimbingan.show' for request_id " . $event['request_id'] . ": " . $e->getMessage());
                                                        $detailRoute = '#'; // Tetap fallback jika ada error
                                                    }
                                                }
                                            @endphp
                                            <a href="{{ $detailRoute }}"
                                               class="event-indicator {{ $indicatorClass }} rounded px-1 small d-block mb-1"
                                               title="Lihat Detail: {{ $eventTitle }}{{ $eventTime }}{{ $eventCatatan }}">
                                                {{ Str::limit($eventTitle, 8) }}
                                            </a>
                                        @endforeach
                                    @endif
                                    {{-- Akhir Indikator Event --}}
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
                    @else
                    <p class="text-center text-muted">Data kalender tidak tersedia. Pastikan variabel (cal_days, cal_currentMonthDateObject, today, events) dikirim dari controller.</p>
                    @endif
                </div>
            </div>

            {{-- LEGENDA KALENDER --}}
            <div class="card mt-3">
                <div class="card-header">Legenda Kalender</div>
                <div class="card-body small">
                    <div class="d-flex align-items-center mb-2"><span style="width:20px;height:20px;background-color:#d4edda;border:1px solid #c3e6cb;" class="me-2"></span>Sel Bimbingan Disetujui</div>
                    <div class="d-flex align-items-center mb-2"><span style="width:20px;height:20px;background-color:#cce5ff;border:1px solid #b8daff;" class="me-2"></span>Sel Bimbingan Dijadwalkan Ulang</div>
                    <hr class="my-1">
                    <div class="d-flex align-items-center mb-2"><span class="event-indicator bimbingan-approved-indicator rounded px-1 me-2" style="display:inline-block;padding:1px 4px!important;">Event</span>Indikator Bimbingan Disetujui</div>
                    <div class="d-flex align-items-center mb-2"><span class="event-indicator bimbingan-rescheduled-indicator rounded px-1 me-2" style="display:inline-block;padding:1px 4px!important;">Event</span>Indikator Bimbingan Dijadwalkan Ulang</div>
                    <div class="d-flex align-items-center"><span style="width:20px;height:20px;border:2px solid #007bff;background-color:#0d6efd;" class="me-2 position-relative"><span style="color:white;font-size:0.7em;position:absolute;top:0;left:3px;font-weight:bold;">T</span></span>Hari Ini (Tanpa Jadwal)</div>
                </div>
            </div>
            {{-- AKHIR KALENDER & LEGENDA --}}
        </div>

        {{-- Kolom Kanan: Aksi Cepat, Dokumen Review, Mahasiswa Aktif (KODE ASLI ANDA) --}}
        <div class="col-md-4">
            {{-- KODE ASLI ANDA UNTUK KOLOM KANAN --}}
            <div class="card mb-3">
                <div class="card-header">
                    Aksi Cepat
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('dosen.request-judul.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-alt me-2"></i>Daftar Request Judul
                    </a>
                    <a href="{{ route('dosen.request-bimbingan.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-comments me-2"></i>Daftar Request Bimbingan
                    </a>
                    <a href="{{ route('dosen.history-bimbingan.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i>Manajemen Riwayat Bimbingan
                    </a>
                    <a href="{{ route('dosen.review-dokumen.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-folder-open me-2"></i>Review Dokumen Mahasiswa
                    </a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-hourglass-half me-2"></i>Dokumen Menunggu Review Anda
                </div>
                <div class="card-body p-0">
                    {{-- Pastikan variabel $dokumenPendingReview dikirim dari DashboardController --}}
                    @if(isset($dokumenPendingReview) && $dokumenPendingReview->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($dokumenPendingReview as $dokumen)
                                <li class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <a href="{{ route('dosen.review-dokumen.proses', $dokumen->id) }}" title="Review {{ $dokumen->jenisDokumen->nama_jenis ?? '' }} dari {{ $dokumen->mahasiswa->user->name ?? '' }}">
                                                {{ $dokumen->jenisDokumen->nama_jenis ?? 'Jenis Tidak Diketahui' }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $dokumen->updated_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 small">
                                        Oleh: {{ $dokumen->mahasiswa->user->name ?? 'N/A' }} ({{ $dokumen->mahasiswa->nim ?? 'N/A' }})
                                        <br>
                                        File: <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank">{{ Str::limit($dokumen->nama_file_asli ?? $dokumen->nama_file, 25) }}</a>
                                        (v{{ $dokumen->versi }})
                                    </p>
                                    <a href="{{ route('dosen.review-dokumen.proses', $dokumen->id) }}" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="fas fa-search-plus me-1"></i> Proses Review
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-3">
                            <p class="text-muted mb-0">Tidak ada dokumen yang menunggu review dari mahasiswa bimbingan Anda.</p>
                        </div>
                    @endif
                </div>
                 @if(isset($dokumenPendingReview) && $dokumenPendingReview->count() > 0)
                <div class="card-footer text-center">
                    <a href="{{ route('dosen.review-dokumen.index') }}" class="small">Lihat Semua Dokumen Pending</a>
                </div>
                @endif
            </div>

            <div class="card">
                <div class="card-header">
                    Mahasiswa Bimbingan Aktif
                </div>
                <div class="card-body">
                    {{-- Pastikan variabel $mahasiswa_bimbingan dikirim dari DashboardController --}}
                    @if(isset($mahasiswa_bimbingan) && $mahasiswa_bimbingan->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($mahasiswa_bimbingan as $mhs)
                                <li class="list-group-item px-0 py-2">
                                    {{-- Buat link ke detail mahasiswa jika ada --}}
                                    <a href="#">{{ $mhs->user->name ?? 'Nama Tidak Ada' }} ({{ $mhs->nim ?? 'NIM Tidak Ada' }})</a>
                                    <br><small class="text-muted">Status: {{ Str::title(str_replace('_', ' ', $mhs->status_proyek_akhir ?? 'N/A')) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">Belum ada mahasiswa bimbingan aktif.</p>
                    @endif
                </div>
            </div>
            {{-- AKHIR KODE ASLI KOLOM KANAN --}}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Kalender Styles */
    .calendar-table td, .calendar-table th { height: 95px; vertical-align: top; padding: 0.35rem; }
    .calendar-table .day-number { font-weight: bold; font-size: 0.9em; text-align: left; padding-left: 5px; margin-bottom: 3px; }
    .calendar-table .other-month { background-color: #f8f9fa !important; }
    .calendar-table .other-month .day-number { color: #ccc !important; }

    /* Hari Ini */
    .calendar-table td.today { border: 2px solid #007bff !important; }
    .calendar-table td.text-white-custom .day-number, .calendar-table td.bg-primary .day-number { color: white !important; }

    /* Background Sel Bimbingan */
    .bimbingan-approved-bg { background-color: #d4edda !important; } /* Hijau muda */
    .bimbingan-approved-bg .day-number { color: #155724 !important; } /* Teks hijau tua */
    .bimbingan-rescheduled-bg { background-color: #cce5ff !important; } /* Biru muda */
    .bimbingan-rescheduled-bg .day-number { color: #004085 !important; } /* Teks biru tua */

    /* Indikator Event */
    .event-indicator { font-size: 0.7em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; color: white; text-decoration: none; padding: 1px 4px !important; }
    .event-indicator:hover { opacity: 0.85; text-decoration: none; color: white; } /* Pastikan hover juga tidak ada underline */
    .bimbingan-approved-indicator { background-color: #28a745 !important; } /* Hijau tua */
    .bimbingan-rescheduled-indicator { background-color: #17a2b8 !important; } /* Biru tua */
</style>
@endpush
