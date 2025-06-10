<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dosen;
use App\Models\RequestJudul;
use App\Notifications\RequestJudulNotification;

class RequestJudulController extends Controller
{
    /**
     * Helper untuk otorisasi akses kelompok.
     * Cek apakah request ini dibuat oleh salah satu anggota kelompok mahasiswa yang login.
     */
    private function authorizeGroupAccess(RequestJudul $requestJudul)
    {
        $mahasiswaLogin = Auth::user()->mahasiswa;
        if (!$mahasiswaLogin) {
            return false;
        }
        // Ambil semua ID anggota kelompok dari mahasiswa yang sedang login
        $anggotaIds = $mahasiswaLogin->semuaAnggotaKelompok()->pluck('id')->all();

        // Cek apakah ID pembuat request ada di dalam daftar anggota kelompok
        return in_array($requestJudul->mahasiswa_id, $anggotaIds);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Ambil ID semua anggota kelompok
        $anggotaIds = $mahasiswa->semuaAnggotaKelompok()->pluck('id');

        // Ambil semua request judul yang dibuat oleh salah satu anggota kelompok
        $requests = RequestJudul::whereIn('mahasiswa_id', $anggotaIds)
                                ->with(['dosenTujuan.user', 'mahasiswa.user:id,name'])
                                ->latest()
                                ->paginate(10);

        // Cek apakah kelompok sudah punya judul yang disetujui atau sudah masuk tahap bimbingan
        $hasApprovedTitle = RequestJudul::whereIn('mahasiswa_id', $anggotaIds)->where('status', 'approved')->exists();
        $isPastTitleStage = in_array($mahasiswa->status_proyek_akhir, ['bimbingan', 'selesai', 'revisi']);
        $canRequestNewTitle = !$hasApprovedTitle && !$isPastTitleStage;

        return view('mahasiswa.request_judul.index', compact('requests', 'canRequestNewTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa || !$mahasiswa->prodi_id) {
            return redirect()->route('dashboard')->with('error', 'Informasi program studi Anda tidak lengkap.');
        }

        // Ambil anggota kelompok untuk ditampilkan di view
        $anggotaKelompok = $mahasiswa->semuaAnggotaKelompok()->load('user:id,name');

        // Ambil daftar dosen dari prodi yang sama
        $dosenList = Dosen::where('prodi_id', $mahasiswa->prodi_id)
                            ->whereHas('user')
                            ->with('user:id,name')
                            ->get()
                            ->sortBy('user.name');

        if ($dosenList->isEmpty()) {
            return redirect()->route('mahasiswa.request-judul.index')
                             ->with('error', 'Tidak ada dosen yang tersedia untuk program studi Anda.');
        }

        return view('mahasiswa.request_judul.create', compact('anggotaKelompok', 'dosenList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Logika store tidak perlu diubah, karena request dibuat atas nama satu user
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa || !$mahasiswa->prodi_id) {
            return redirect()->back()->withInput()->with('error', 'Informasi program studi Anda tidak lengkap.');
        }

        $validatedData = $request->validate([
            'judul_diajukan' => 'required|string|max:255|min:10',
            'deskripsi' => 'required|string|min:20',
            'dosen_tujuan_id' => 'required|exists:dosen,id',
        ]);

        $dosenDipilih = Dosen::find($validatedData['dosen_tujuan_id']);
        if (!$dosenDipilih || $dosenDipilih->prodi_id !== $mahasiswa->prodi_id) {
            return redirect()->back()->withInput()->with('error', 'Dosen yang Anda pilih tidak valid.');
        }

        $requestJudul = RequestJudul::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_tujuan_id' => $validatedData['dosen_tujuan_id'],
            'judul_diajukan' => $validatedData['judul_diajukan'],
            'deskripsi' => $validatedData['deskripsi'],
            'status' => 'pending',
        ]);

        if ($dosenDipilih->user) {
            $dosenDipilih->user->notify(new RequestJudulNotification($requestJudul, 'to_dosen'));
        }

        return redirect()->route('mahasiswa.request-judul.index')
                         ->with('success', 'Pengajuan judul berhasil dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RequestJudul $requestJudul)
    {
        if (!$this->authorizeGroupAccess($requestJudul)) {
            abort(403, 'Akses ditolak.');
        }

        $requestJudul->load('mahasiswa.user', 'mahasiswa.prodi', 'dosenTujuan.user');
        return view('mahasiswa.request_judul.show', compact('requestJudul'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RequestJudul $requestJudul)
    {
        if (!$this->authorizeGroupAccess($requestJudul)) {
            abort(403, 'Akses ditolak.');
        }

        if ($requestJudul->status !== 'pending') {
            return redirect()->route('mahasiswa.request-judul.show', $requestJudul->id)
                             ->with('error', 'Pengajuan ini tidak dapat diedit.');
        }

        $mahasiswaPembuat = $requestJudul->mahasiswa;
        $anggotaKelompok = $mahasiswaPembuat->semuaAnggotaKelompok()->load('user:id,name');
        $dosenList = Dosen::where('prodi_id', $mahasiswaPembuat->prodi_id)
                                ->whereHas('user')
                                ->with('user:id,name')
                                ->get()
                                ->sortBy('user.name');

        return view('mahasiswa.request_judul.edit', compact('requestJudul', 'anggotaKelompok', 'dosenList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RequestJudul $requestJudul)
    {
        if (!$this->authorizeGroupAccess($requestJudul)) {
            abort(403, 'Akses ditolak.');
        }

        if ($requestJudul->status !== 'pending') {
            return redirect()->route('mahasiswa.request-judul.show', $requestJudul->id)
                             ->with('error', 'Pengajuan ini tidak dapat diupdate.');
        }

        $validatedData = $request->validate([
            'judul_diajukan' => 'required|string|max:255|min:10',
            'deskripsi' => 'required|string|min:20',
            'dosen_tujuan_id' => 'required|exists:dosen,id',
        ]);

        $requestJudul->update($validatedData);

        return redirect()->route('mahasiswa.request-judul.show', $requestJudul->id)
                         ->with('success', 'Pengajuan judul berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequestJudul $requestJudul)
    {
        if (!$this->authorizeGroupAccess($requestJudul)) {
            abort(403, 'Akses ditolak.');
        }

        if ($requestJudul->status !== 'pending') {
            return redirect()->route('mahasiswa.request-judul.show', $requestJudul->id)
                             ->with('error', 'Pengajuan ini tidak dapat dibatalkan.');
        }

        $requestJudul->delete();

        return redirect()->route('mahasiswa.request-judul.index')
                         ->with('success', 'Pengajuan judul berhasil dibatalkan.');
    }
}
