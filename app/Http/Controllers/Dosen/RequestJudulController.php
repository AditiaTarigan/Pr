<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\RequestJudul;
use App\Notifications\RequestJudulNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RequestJudulController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan request judul yang ditujukan kepada dosen yang sedang login.
     */
    public function index(Request $request)
    {
        // ... (Tidak ada perubahan di sini, sudah benar)
        $userDosen = Auth::user();
        if (!$userDosen || !$userDosen->dosen) {
            return redirect()->route('dashboard')->with('error', 'Data profil dosen Anda tidak ditemukan.');
        }
        $dosenId = $userDosen->dosen->id;

        $query = RequestJudul::where('dosen_tujuan_id', $dosenId);

        if ($request->has('status') && in_array($request->input('status'), ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', 'pending');
        }

        $requests = $query->with(['mahasiswa.user:id,name', 'mahasiswa.prodi:id,nama_prodi'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        $filterStatus = $request->input('status', 'pending');

        return view('dosen.request_judul.index', compact('requests', 'filterStatus'));
    }

    /**
     * Display the specified resource.
     */
    public function show(RequestJudul $requestJudul)
    {
        // ... (Tidak ada perubahan di sini, sudah benar)
        $userDosen = Auth::user();
        if (!$userDosen || !$userDosen->dosen || $requestJudul->dosen_tujuan_id !== $userDosen->dosen->id) {
            abort(403, 'Anda tidak berhak mengakses detail pengajuan ini.');
        }
        $requestJudul->load(['mahasiswa.user:id,name,email', 'mahasiswa.prodi:id,nama_prodi,fakultas']);
        return view('dosen.request_judul.show', compact('requestJudul'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RequestJudul $requestJudul)
    {
         // ... (Tidak ada perubahan di sini, sudah benar)
        $userDosen = Auth::user();
        if (!$userDosen || !$userDosen->dosen || $requestJudul->dosen_tujuan_id !== $userDosen->dosen->id) {
            abort(403, 'Anda tidak berhak mengubah pengajuan ini.');
        }
        if ($requestJudul->status !== 'pending') {
             return redirect()->route('dosen.request-judul.show', $requestJudul->id)
                              ->with('warning', 'Pengajuan judul ini sudah diproses.');
        }
        $requestJudul->load(['mahasiswa.user:id,name', 'mahasiswa.prodi:id,nama_prodi']);
        return view('dosen.request_judul.edit', compact('requestJudul'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RequestJudul $requestJudul)
    {
        // Otorisasi
        $userDosen = Auth::user();
        if (!$userDosen || !$userDosen->dosen || $requestJudul->dosen_tujuan_id !== $userDosen->dosen->id) {
            abort(403, 'Anda tidak berhak mengubah pengajuan ini.');
        }

        if ($requestJudul->status !== 'pending') {
             return redirect()->route('dosen.request-judul.show', $requestJudul->id)
                              ->with('warning', 'Pengajuan judul ini sudah diproses.');
        }

        $validatedData = $request->validate([
            'status' => 'required|in:approved,rejected', // Sederhanakan, revisi bisa jadi status terpisah
            'catatan_dosen' => 'nullable|string',
        ]);

        $requestJudul->status = $validatedData['status'];
        $requestJudul->catatan_dosen = $validatedData['catatan_dosen'] ?? null;
        $requestJudul->save();

        // --- INI BAGIAN UTAMA YANG DIPERBAIKI ---
        // Dapatkan mahasiswa pembuat request sebagai titik awal
        $mahasiswaPembuat = $requestJudul->mahasiswa;

        if ($mahasiswaPembuat) {
            // Ambil SEMUA anggota kelompok, termasuk si pembuat
            $semuaAnggotaKelompok = $mahasiswaPembuat->semuaAnggotaKelompok();

            // Jika statusnya 'approved', update semua anggota kelompok
            if ($validatedData['status'] === 'approved') {
                foreach ($semuaAnggotaKelompok as $anggota) {
                    $anggota->status_proyek_akhir = 'bimbingan'; // Set status proyek menjadi bimbingan
                    $anggota->judul_proyek_akhir = $requestJudul->judul_diajukan; // Set judul yang sama untuk semua
                    $anggota->dosen_pembimbing_id = $requestJudul->dosen_tujuan_id; // Set dosen pembimbing yang sama
                    $anggota->save();
                }

                // Setelah semua di-approve, tolak semua request judul PENDING lainnya dari kelompok ini
                $anggotaIds = $semuaAnggotaKelompok->pluck('id');
                RequestJudul::whereIn('mahasiswa_id', $anggotaIds)
                            ->where('status', 'pending')
                            ->where('id', '!=', $requestJudul->id) // Jangan tolak request yang baru saja diapprove
                            ->update(['status' => 'rejected', 'catatan_dosen' => 'Ditolak otomatis karena judul lain dari kelompok Anda telah disetujui.']);
            }

            // Kirim notifikasi ke SEMUA anggota kelompok
            foreach ($semuaAnggotaKelompok as $anggota) {
                if ($anggota->user) {
                    $anggota->user->notify(new RequestJudulNotification($requestJudul, 'to_mahasiswa'));
                }
            }
        }
        // --- AKHIR DARI BAGIAN YANG DIPERBAIKI ---

        return redirect()->route('dosen.request-judul.index')
                         ->with('success', 'Status pengajuan judul berhasil diperbarui.');
    }
}
