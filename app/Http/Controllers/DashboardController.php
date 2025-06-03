<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\User;
use App\Models\Prodi; // Asumsi ada model Prodi
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\DokumenProyekAkhir; // Asumsi ada model DokumenProyekAkhir
use App\Models\RequestBimbingan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Menghasilkan data umum untuk kalender.
     */
    private function getCalendarData(Request $request): array
    {
        $requestedMonth = (int) $request->input('cal_month', Carbon::now()->month);
        $requestedYear = (int) $request->input('cal_year', Carbon::now()->year);
        try {
            $currentMonthDate = Carbon::createFromDate($requestedYear, $requestedMonth, 1)->startOfDay();
        } catch (\Exception $e) {
            $now = Carbon::now();
            $currentMonthDate = Carbon::createFromDate($now->year, $now->month, 1)->startOfDay();
            Log::warning("DashboardController::getCalendarData - Invalid date requested, defaulting.", ['req_year' => $requestedYear, 'req_month' => $requestedMonth]);
        }
        $previousMonthDate = $currentMonthDate->copy()->subMonthNoOverflow();
        $nextMonthDate = $currentMonthDate->copy()->addMonthNoOverflow();
        $startDate = $currentMonthDate->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $endDate = $currentMonthDate->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        $days = CarbonPeriod::create($startDate, $endDate);
        return [
            'cal_currentMonthDateObject' => $currentMonthDate,
            'cal_monthName' => $currentMonthDate->translatedFormat('F'),
            'cal_year' => $currentMonthDate->year,
            'cal_days' => $days,
            'cal_previousMonthLinkParams' => ['cal_month' => $previousMonthDate->month, 'cal_year' => $previousMonthDate->year],
            'cal_nextMonthLinkParams' => ['cal_month' => $nextMonthDate->month, 'cal_year' => $nextMonthDate->year],
            'today' => Carbon::today(),
        ];
    }

    /**
     * Mengambil data statistik untuk dashboard admin.
     */
    private function getAdminDashboardData(): array
    {
        $data = [];
        $data['totalUsers'] = User::count();
        $data['totalProdi'] = Prodi::count();
        $data['totalDosen'] = Dosen::count();
        $data['totalMahasiswa'] = Mahasiswa::count();
        return $data;
    }

    /**
     * Mengambil data spesifik untuk dashboard dosen.
     */
    private function getDosenDashboardData(User $user, Carbon $currentMonthDateObject): array
    {
        $data = ['events' => []];
        $dosen = $user->dosen;

        if ($dosen) {
            $mahasiswaBimbinganIds = Mahasiswa::where('dosen_pembimbing_id', $dosen->id)->pluck('id');

            // Data Dokumen Pending Review
            $data['dokumenPendingReview'] = $mahasiswaBimbinganIds->isNotEmpty() ?
                DokumenProyekAkhir::whereIn('mahasiswa_id', $mahasiswaBimbinganIds)
                    ->whereIn('status_review', ['pending', 'revision_needed'])
                    // Saat mengakses di blade: $dokumen->mahasiswa->user->name DAN $dokumen->mahasiswa->nim
                    ->with(['mahasiswa.user:id,name', 'mahasiswa:id,nim', 'jenisDokumen:id,nama_jenis'])
                    ->latest('updated_at')->take(5)->get() : collect();

            // Data Mahasiswa Bimbingan Aktif
            $data['mahasiswa_bimbingan'] = Mahasiswa::where('dosen_pembimbing_id', $dosen->id)
                ->whereIn('status_proyek_akhir', ['bimbingan', 'pengajuan_judul', 'revisi'])
                // KOREKSI DI SINI: Hanya ambil 'id' dan 'name' dari 'user'
                // Kolom 'nim' dan 'status_proyek_akhir' akan diakses dari objek $mhs (Mahasiswa) itu sendiri
                ->with(['user:id,name'])
                ->orderBy('created_at', 'desc')
                ->get();
            // Cara akses di Blade Dosen:
            // foreach ($mahasiswa_bimbingan as $mhs)
            //   $mhs->user->name
            //   $mhs->nim  <-- dari objek Mahasiswa
            //   $mhs->status_proyek_akhir <-- dari objek Mahasiswa
            // endforeach

            // Kalender Events (Kode ini sudah benar dari sebelumnya)
            $requestBimbingans = RequestBimbingan::where('dosen_id', $dosen->id)
                ->whereIn('status_request', ['approved', 'rescheduled'])
                ->where(function ($query) use ($currentMonthDateObject) {
                    $query->where(function ($subQuery) use ($currentMonthDateObject) {
                        $subQuery->where('status_request', 'approved')->whereYear('tanggal_usulan', $currentMonthDateObject->year)->whereMonth('tanggal_usulan', $currentMonthDateObject->month);
                    })->orWhere(function ($subQuery) use ($currentMonthDateObject) {
                        $subQuery->where('status_request', 'rescheduled')->whereNotNull('tanggal_dosen')->whereYear('tanggal_dosen', $currentMonthDateObject->year)->whereMonth('tanggal_dosen', $currentMonthDateObject->month);
                    });
                })->get();

            $events = [];
            foreach ($requestBimbingans as $bimbingan) {
                $effectiveDate = null; $effectiveJam = null; $isRescheduled = false;
                if ($bimbingan->status_request == 'approved' && $bimbingan->tanggal_usulan) {
                    $effectiveDate = $bimbingan->tanggal_usulan; $effectiveJam = $bimbingan->jam_usulan;
                } elseif ($bimbingan->status_request == 'rescheduled' && $bimbingan->tanggal_dosen) {
                    $effectiveDate = $bimbingan->tanggal_dosen; $effectiveJam = $bimbingan->jam_dosen ?? $bimbingan->jam_usulan; $isRescheduled = true;
                } else { continue; }
                $dateKey = $effectiveDate instanceof Carbon ? $effectiveDate->toDateString() : Carbon::parse($effectiveDate)->toDateString();
                if (!isset($events[$dateKey])) { $events[$dateKey] = []; }
                $formattedJam = null; if ($effectiveJam) { try { $formattedJam = Carbon::parse($effectiveJam)->format('H:i'); } catch (\Exception $e) { $formattedJam = null; }}
                $events[$dateKey][] = [
                    'request_id' => $bimbingan->id, 'title' => $bimbingan->topik_bimbingan ?: 'Bimbingan',
                    'status' => $bimbingan->status_request, 'jam' => $formattedJam,
                    'lokasi_usulan' => $bimbingan->lokasi_usulan, 'catatan_dosen' => $isRescheduled ? $bimbingan->catatan_dosen : null,
                ];
            }
            $data['events'] = $events;
        } else {
            Log::warning("Dosen dashboard: Dosen profile not found for User ID: " . $user->id);
            $data['dokumenPendingReview'] = collect(); $data['mahasiswa_bimbingan'] = collect();
        }
        return $data;
    }

    /**
     * Mengambil data spesifik untuk dashboard mahasiswa.
     */
    private function getMahasiswaDashboardData(User $user, Carbon $currentMonthDateObject): array
    {
        // Kode ini sudah benar dari sebelumnya
        $data = ['events' => []];
        $mahasiswa = $user->mahasiswa()->with(['prodi:id,nama_prodi', 'dosenPembimbing.user:id,name'])->first();

        if ($mahasiswa) {
            $data['mahasiswa'] = $mahasiswa;
            $data['status_proyek_akhir'] = $mahasiswa->status_proyek_akhir ?? 'N/A';

            $requestBimbingans = RequestBimbingan::where('mahasiswa_id', $mahasiswa->id)
                ->whereIn('status_request', ['approved', 'rescheduled'])
                ->where(function ($query) use ($currentMonthDateObject) {
                    $query->where(function ($subQuery) use ($currentMonthDateObject) {
                        $subQuery->where('status_request', 'approved')->whereYear('tanggal_usulan', $currentMonthDateObject->year)->whereMonth('tanggal_usulan', $currentMonthDateObject->month);
                    })->orWhere(function ($subQuery) use ($currentMonthDateObject) {
                        $subQuery->where('status_request', 'rescheduled')->whereNotNull('tanggal_dosen')->whereYear('tanggal_dosen', $currentMonthDateObject->year)->whereMonth('tanggal_dosen', $currentMonthDateObject->month);
                    });
                })->with('dosen.user:id,name')->get();

            $events = [];
            foreach ($requestBimbingans as $bimbingan) {
                $effectiveDate = null; $effectiveJam = null;
                if ($bimbingan->status_request == 'approved' && $bimbingan->tanggal_usulan) {
                    $effectiveDate = $bimbingan->tanggal_usulan; $effectiveJam = $bimbingan->jam_usulan;
                } elseif ($bimbingan->status_request == 'rescheduled' && $bimbingan->tanggal_dosen) {
                    $effectiveDate = $bimbingan->tanggal_dosen; $effectiveJam = $bimbingan->jam_dosen ?? $bimbingan->jam_usulan;
                } else { continue; }
                $dateKey = $effectiveDate instanceof Carbon ? $effectiveDate->toDateString() : Carbon::parse($effectiveDate)->toDateString();
                if (!isset($events[$dateKey])) { $events[$dateKey] = []; }
                $formattedJam = null; if ($effectiveJam) { try { $formattedJam = Carbon::parse($effectiveJam)->format('H:i'); } catch (\Exception $e) { $formattedJam = null; }}
                $events[$dateKey][] = [
                    'request_id' => $bimbingan->id, 'title' => $bimbingan->topik_bimbingan ?: 'Bimbingan',
                    'status' => $bimbingan->status_request, 'jam' => $formattedJam,
                    'lokasi' => $bimbingan->lokasi_usulan,
                    'dosen_nama' => $bimbingan->dosen->user->name ?? 'N/A',
                ];
            }
            $data['events'] = $events;
        } else {
            Log::warning("Mahasiswa dashboard: Mahasiswa profile not found for User ID: " . $user->id);
            $data['status_proyek_akhir'] = 'N/A'; $data['mahasiswa'] = null;
        }
        return $data;
    }

    /**
     * Menampilkan halaman dashboard berdasarkan role user.
     */
    public function index(Request $request)
    {
        // Kode ini sudah benar dari sebelumnya
        $user = Auth::user(); $viewData = [];
        if (!$user) { return redirect()->route('login')->withErrors('Sesi tidak valid.'); }

        $calendarData = $this->getCalendarData($request);
        $viewData = array_merge($viewData, $calendarData);

        if ($user->role === 'admin') {
            $adminData = $this->getAdminDashboardData();
            $viewData = array_merge($viewData, $adminData);
            return view('dashboards.admin', $viewData);
        } elseif ($user->role === 'dosen') {
            $dosenData = $this->getDosenDashboardData($user, $calendarData['cal_currentMonthDateObject']);
            $viewData = array_merge($viewData, $dosenData);
            return view('dashboards.dosen', $viewData);
        } elseif ($user->role === 'mahasiswa') {
            $mahasiswaData = $this->getMahasiswaDashboardData($user, $calendarData['cal_currentMonthDateObject']);
            $viewData = array_merge($viewData, $mahasiswaData);
            return view('dashboards.mahasiswa', $viewData);
        }
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('login')->withErrors('Peran pengguna tidak valid.');
    }
}
