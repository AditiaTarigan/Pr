<?php

namespace App\Http\Controllers;

// PERBAIKAN: Memastikan semua 'use' statement benar dan lengkap
use Illuminate\Http\Request; // Class Request yang benar dari Illuminate
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\User;
use App\Models\Prodi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\DokumenProyekAkhir;
use App\Models\RequestBimbingan;
// 'use Illuminate\Support\Str;' dihapus karena tidak digunakan

class DashboardController extends Controller
{
    /**
     * Menghasilkan data umum untuk kalender.
     */
    private function getCalendarData(Request $request): array
    {
        // Tidak ada perubahan logika di sini, hanya memastikan type-hint $request benar
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
        // Tidak ada perubahan di sini
        $data = ['events' => []];
        $dosen = $user->dosen;

        if ($dosen) {
            $mahasiswaBimbinganIds = Mahasiswa::where('dosen_pembimbing_id', $dosen->id)->pluck('id');

            $data['dokumenPendingReview'] = $mahasiswaBimbinganIds->isNotEmpty() ?
                DokumenProyekAkhir::whereIn('mahasiswa_id', $mahasiswaBimbinganIds)
                    ->whereIn('status_review', ['pending', 'revision_needed'])
                    ->with(['mahasiswa.user:id,name', 'mahasiswa:id,nim', 'jenisDokumen:id,nama_jenis'])
                    ->latest('updated_at')->take(5)->get() : collect();

            $data['mahasiswa_bimbingan'] = Mahasiswa::where('dosen_pembimbing_id', $dosen->id)
                ->whereIn('status_proyek_akhir', ['bimbingan', 'pengajuan_judul', 'revisi'])
                ->with(['user:id,name'])
                ->orderBy('created_at', 'desc')
                ->get();

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
                    'lokasi' => $bimbingan->lokasi_bimbingan ?? $bimbingan->lokasi_usulan,
                    'catatan_dosen' => $isRescheduled ? $bimbingan->catatan_dosen : null,
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
        // Logika di sini sudah benar dari revisi sebelumnya
        $data = ['events' => []];
        $mahasiswa = $user->mahasiswa()->with(['prodi:id,nama_prodi', 'dosenPembimbing.user:id,name'])->first();

        if ($mahasiswa) {
            $data['mahasiswa'] = $mahasiswa;
            $data['status_proyek_akhir'] = $mahasiswa->status_proyek_akhir ?? 'N/A';

            $anggotaKelompokIds = $mahasiswa->semuaAnggotaKelompok()->pluck('id');

            $requestBimbingans = RequestBimbingan::whereIn('mahasiswa_id', $anggotaKelompokIds)
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

                $effectiveLokasi = $bimbingan->lokasi_bimbingan ?? $bimbingan->lokasi_usulan;
                $dateKey = $effectiveDate instanceof Carbon ? $effectiveDate->toDateString() : Carbon::parse($effectiveDate)->toDateString();

                if (!isset($events[$dateKey])) { $events[$dateKey] = []; }
                $formattedJam = null; if ($effectiveJam) { try { $formattedJam = Carbon::parse($effectiveJam)->format('H:i'); } catch (\Exception $e) { $formattedJam = null; }}
                $events[$dateKey][] = [
                    'request_id' => $bimbingan->id, 'title' => $bimbingan->topik_bimbingan ?: 'Bimbingan',
                    'status' => $bimbingan->status_request, 'jam' => $formattedJam,
                    'lokasi' => $effectiveLokasi,
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
        // Tidak ada perubahan logika di sini, hanya memastikan type-hint $request benar
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
