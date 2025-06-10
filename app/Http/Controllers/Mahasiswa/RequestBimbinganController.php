<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\RequestBimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\RequestBimbinganNotification;

class RequestBimbinganController extends Controller
{
    private function getMahasiswaData()
    {
        return Auth::user()->mahasiswa;
    }

    private function authorizeGroupAccess(RequestBimbingan $requestBimbingan)
    {
        $mahasiswaLogin = $this->getMahasiswaData();
        if (!$mahasiswaLogin) return false;

        $anggotaIds = $mahasiswaLogin->semuaAnggotaKelompok()->pluck('id')->all();
        return in_array($requestBimbingan->mahasiswa_id, $anggotaIds);
    }

    public function index()
    {
        $mahasiswa = $this->getMahasiswaData();
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data profil mahasiswa tidak ditemukan.');
        }

        $hasDosbing = (bool)$mahasiswa->dosen_pembimbing_id;
        $requests = collect(); // Default empty collection

        if ($hasDosbing) {
            $anggotaIds = $mahasiswa->semuaAnggotaKelompok()->pluck('id');
            $requests = RequestBimbingan::whereIn('mahasiswa_id', $anggotaIds)
                                        ->with(['dosen.user:id,name', 'mahasiswa.user:id,name'])
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(10);
        }

        return view('mahasiswa.request_bimbingan.index', compact('requests', 'hasDosbing'));
    }

    public function create()
    {
        $mahasiswa = $this->getMahasiswaData();
        if (!$mahasiswa || !$mahasiswa->dosen_pembimbing_id) {
             return redirect()->route('mahasiswa.request-bimbingan.index')
                             ->with('warning', 'Dosen pembimbing Anda belum ditetapkan.');
        }

        $dosenPembimbing = $mahasiswa->dosenPembimbing()->with('user:id,name')->firstOrFail();
        $anggotaKelompok = $mahasiswa->semuaAnggotaKelompok()->load('user:id,name');

        return view('mahasiswa.request_bimbingan.create', compact('dosenPembimbing', 'anggotaKelompok'));
    }

    public function store(Request $request)
    {
        $mahasiswa = $this->getMahasiswaData();
        if (!$mahasiswa || !$mahasiswa->dosen_pembimbing_id) {
            return redirect()->back()->withInput()->with('error', 'Dosen pembimbing Anda belum ditetapkan.');
        }

        $validatedData = $request->validate([
            'tanggal_usulan' => 'required|date|after_or_equal:today',
            'jam_usulan' => 'required|date_format:H:i',
            'topik_bimbingan' => 'required|string|min:10|max:500',
            'lokasi_usulan' => 'nullable|string|max:255',
            'catatan_mahasiswa' => 'nullable|string|max:1000',
        ]);

        $requestBimbingan = RequestBimbingan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $mahasiswa->dosen_pembimbing_id,
            'tanggal_usulan' => $validatedData['tanggal_usulan'],
            'jam_usulan' => $validatedData['jam_usulan'],
            'topik_bimbingan' => $validatedData['topik_bimbingan'],
            'lokasi_usulan' => $validatedData['lokasi_usulan'] ?? null,
            'catatan_mahasiswa' => $validatedData['catatan_mahasiswa'] ?? null,
            'status_request' => 'pending',
        ]);

        if ($mahasiswa->dosenPembimbing->user) {
            $mahasiswa->dosenPembimbing->user->notify(new RequestBimbinganNotification($requestBimbingan, 'to_dosen'));
        }

        return redirect()->route('mahasiswa.request-bimbingan.index')
                         ->with('success', 'Pengajuan bimbingan berhasil dikirim.');
    }

    public function show(RequestBimbingan $requestBimbingan)
    {
        if (!$this->authorizeGroupAccess($requestBimbingan)) {
            abort(403, 'Akses ditolak.');
        }

        $requestBimbingan->load(['dosen.user:id,name', 'mahasiswa.user:id,name']);
        return view('mahasiswa.request_bimbingan.show', compact('requestBimbingan'));
    }

    public function edit(RequestBimbingan $requestBimbingan)
    {
        if (!$this->authorizeGroupAccess($requestBimbingan)) {
            abort(403, 'Akses ditolak.');
        }

        if ($requestBimbingan->status_request !== 'pending') {
            return redirect()->route('mahasiswa.request-bimbingan.show', $requestBimbingan->id)
                             ->with('warning', 'Pengajuan ini tidak dapat diedit.');
        }

        $dosenPembimbing = $this->getMahasiswaData()->dosenPembimbing()->with('user:id,name')->firstOrFail();
        $anggotaKelompok = $requestBimbingan->mahasiswa->semuaAnggotaKelompok()->load('user:id,name');

        return view('mahasiswa.request_bimbingan.edit', compact('requestBimbingan', 'dosenPembimbing', 'anggotaKelompok'));
    }

    public function update(Request $request, RequestBimbingan $requestBimbingan)
    {
        if (!$this->authorizeGroupAccess($requestBimbingan)) {
            abort(403, 'Akses ditolak.');
        }

        if ($requestBimbingan->status_request !== 'pending') {
            return redirect()->route('mahasiswa.request-bimbingan.show', $requestBimbingan->id)
                             ->with('warning', 'Pengajuan ini tidak dapat diupdate.');
        }

        $validatedData = $request->validate([
            'tanggal_usulan' => 'required|date|after_or_equal:today',
            'jam_usulan' => 'required|date_format:H:i',
            'topik_bimbingan' => 'required|string|min:10|max:500',
            'lokasi_usulan' => 'nullable|string|max:255',
            'catatan_mahasiswa' => 'nullable|string|max:1000',
        ]);

        $requestBimbingan->update($validatedData);

        return redirect()->route('mahasiswa.request-bimbingan.show', $requestBimbingan->id)
                         ->with('success', 'Pengajuan bimbingan berhasil diperbarui.');
    }

    public function destroy(RequestBimbingan $requestBimbingan)
    {
        if (!$this->authorizeGroupAccess($requestBimbingan)) {
            abort(403, 'Akses ditolak.');
        }

        if ($requestBimbingan->status_request !== 'pending') {
            return redirect()->route('mahasiswa.request-bimbingan.show', $requestBimbingan->id)
                             ->with('warning', 'Pengajuan ini tidak dapat dibatalkan.');
        }

        $requestBimbingan->delete();

        return redirect()->route('mahasiswa.request-bimbingan.index')
                         ->with('success', 'Pengajuan bimbingan berhasil dibatalkan.');
    }
}
