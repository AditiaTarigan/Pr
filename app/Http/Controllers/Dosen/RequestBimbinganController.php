<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\RequestBimbingan;
use App\Models\HistoryBimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\RequestBimbinganNotification;

class RequestBimbinganController extends Controller
{
    private function getAuthenticatedDosen()
    {
        $user = Auth::user();
        return $user ? $user->dosen : null;
    }

    public function index(Request $request)
    {
        $dosen = $this->getAuthenticatedDosen();
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data profil dosen Anda tidak ditemukan.');
        }

        $query = RequestBimbingan::where('dosen_id', $dosen->id);
        $filterStatus = $request->input('status', 'pending');

        if (!empty($filterStatus) && in_array($filterStatus, ['pending', 'approved', 'rejected', 'rescheduled'])) {
            $query->where('status_request', $filterStatus);
        }

        $requests = $query->with(['mahasiswa.user:id,name', 'mahasiswa.prodi:id,nama_prodi'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);

        return view('dosen.request_bimbingan.index', compact('requests', 'filterStatus'));
    }

    public function show(RequestBimbingan $requestBimbingan)
    {
        $dosen = $this->getAuthenticatedDosen();
        if (!$dosen || $requestBimbingan->dosen_id !== $dosen->id) {
            abort(403, 'Akses ditolak.');
        }
        $requestBimbingan->load(['mahasiswa.user:id,name,email', 'mahasiswa.prodi:id,nama_prodi']);
        return view('dosen.request_bimbingan.show', compact('requestBimbingan'));
    }

    public function edit(RequestBimbingan $requestBimbingan)
    {
        $dosen = $this->getAuthenticatedDosen();
        if (!$dosen || $requestBimbingan->dosen_id !== $dosen->id) {
            abort(403, 'Akses ditolak.');
        }
        if ($requestBimbingan->status_request !== 'pending') {
             return redirect()->route('dosen.request-bimbingan.show', $requestBimbingan->id)
                              ->with('warning', 'Request bimbingan ini sudah diproses.');
        }
        $requestBimbingan->load(['mahasiswa.user:id,name']);
        return view('dosen.request_bimbingan.edit', compact('requestBimbingan'));
    }

    public function update(Request $request, RequestBimbingan $requestBimbingan)
    {
        $dosen = $this->getAuthenticatedDosen();
        if (!$dosen || $requestBimbingan->dosen_id !== $dosen->id) {
            abort(403, 'Anda tidak berhak memproses request ini.');
        }

        if ($requestBimbingan->status_request !== 'pending') {
             return redirect()->route('dosen.request-bimbingan.show', $requestBimbingan->id)
                              ->with('warning', 'Request bimbingan ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'status_request' => 'required|in:approved,rejected,rescheduled',
            'catatan_dosen' => 'nullable|string|max:1000',
            'tanggal_dosen' => 'nullable|required_if:status_request,rescheduled|date|after_or_equal:today',
            'jam_dosen' => 'nullable|required_if:status_request,rescheduled|date_format:H:i',
        ]);

        DB::beginTransaction();
        try {
            $requestBimbingan->status_request = $validated['status_request'];
            $requestBimbingan->catatan_dosen = $validated['catatan_dosen'];

            if ($validated['status_request'] === 'rescheduled') {
                $requestBimbingan->tanggal_dosen = $validated['tanggal_dosen'];
                $requestBimbingan->jam_dosen = $validated['jam_dosen'];
            } elseif ($validated['status_request'] === 'approved') {
                $requestBimbingan->tanggal_dosen = $requestBimbingan->tanggal_usulan;
                $requestBimbingan->jam_dosen = $requestBimbingan->jam_usulan;
            }
            $requestBimbingan->save();

            $mahasiswaPembuat = $requestBimbingan->mahasiswa;
            if (!$mahasiswaPembuat) {
                throw new \Exception("Data mahasiswa pembuat request tidak ditemukan.");
            }

            $semuaAnggotaKelompok = $mahasiswaPembuat->semuaAnggotaKelompok();

            if ($validated['status_request'] === 'approved') {
                if (!$requestBimbingan->tanggal_dosen || !$requestBimbingan->jam_dosen) {
                    throw new \Exception("Tanggal atau Jam bimbingan final tidak dapat ditentukan.");
                }

                $tanggalBimbinganFinal = Carbon::parse($requestBimbingan->tanggal_dosen->format('Y-m-d') . ' ' . $requestBimbingan->jam_dosen)->format('Y-m-d H:i:s');

                $anggotaIds = $semuaAnggotaKelompok->pluck('id');
                $jumlahAnggota = $anggotaIds->count();
                if ($jumlahAnggota === 0) { throw new \Exception("Tidak ada anggota kelompok yang ditemukan."); }

                $pertemuanKe = (HistoryBimbingan::whereIn('mahasiswa_id', $anggotaIds)->where('dosen_id', $dosen->id)->count() / $jumlahAnggota) + 1;

                $historyRecords = [];
                $now = now();
                foreach ($semuaAnggotaKelompok as $anggota) {
                    $historyRecords[] = [
                        'mahasiswa_id' => $anggota->id,
                        'dosen_id' => $dosen->id,
                        'request_bimbingan_id' => $requestBimbingan->id,
                        'tanggal_bimbingan' => $tanggalBimbinganFinal,
                        'topik' => $requestBimbingan->topik_bimbingan,
                        'pertemuan_ke' => (int) $pertemuanKe,
                        // --- PERBAIKAN DI SINI ---
                        // Menggunakan nilai yang valid sesuai ENUM di database
                        'status_kehadiran' => 'hadir',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                HistoryBimbingan::insert($historyRecords);
            }

            foreach ($semuaAnggotaKelompok as $anggota) {
                if ($anggota->user) {
                    $anggota->user->notify(new RequestBimbinganNotification($requestBimbingan, 'to_mahasiswa'));
                }
            }

            DB::commit();
            return redirect()->route('dosen.request-bimbingan.index')
                             ->with('success', 'Status request bimbingan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saat Dosen (ID: {$dosen->id}) memproses Request Bimbingan (ID: {$requestBimbingan->id}): " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan pada sistem. Silakan coba lagi.');
        }
    }
}
