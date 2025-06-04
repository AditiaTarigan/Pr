<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\DokumenProyekAkhir;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ReviewDokumenController extends Controller
{
    /**
     * Display a listing of documents for review for the authenticated dosen,
     * with optional filtering by status.
     */
    public function index(Request $request)
    {
        $dosen = Auth::user()->dosen;
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan. Harap lengkapi profil Anda.');
        }

        $mahasiswaBimbinganIds = Mahasiswa::where('dosen_pembimbing_id', $dosen->id)->pluck('id');

        if ($mahasiswaBimbinganIds->isEmpty()) {
            return view('dosen.review_dokumen.index', [
                'dokumens' => DokumenProyekAkhir::query()->paginate(10), // Pass an empty paginator
                'filterStatus' => null,
            ]);
        }

        $filterStatus = $request->input('status_review');

        $query = DokumenProyekAkhir::whereIn('mahasiswa_id', $mahasiswaBimbinganIds)
            ->with(['mahasiswa.user', 'jenisDokumen']);

        if (!empty($filterStatus)) {
            $query->where('status_review', $filterStatus);
        } else {
            // Default view: show documents that are 'pending' or 'revision_needed'
            // These are the documents that typically require action.
            $query->whereIn('status_review', ['pending', 'revision_needed']);
        }

        $dokumens = $query->latest('updated_at')
            ->paginate(10);

        return view('dosen.review_dokumen.index', [
            'dokumens' => $dokumens,
            'filterStatus' => $filterStatus,
        ]);
    }

    /**
     * Show the form for processing the review of a specific document.
     */
    public function prosesReview(DokumenProyekAkhir $dokumenProyekAkhir)
    {
        $dosen = Auth::user()->dosen;
        if (!$dosen || !$dokumenProyekAkhir->mahasiswa || $dokumenProyekAkhir->mahasiswa->dosen_pembimbing_id !== $dosen->id) {
            abort(403, 'Anda tidak berhak mereview dokumen ini.');
        }

        // Prevent processing if already approved
        if ($dokumenProyekAkhir->status_review === 'approved') {
            return redirect()->route('dosen.review-dokumen.index')
                             ->with('info', 'Dokumen ini sudah disetujui dan tidak dapat diproses lagi.');
        }
        // Optionally, you might also want to prevent processing if 'rejected'
        // if ($dokumenProyekAkhir->status_review === 'rejected') {
        //     return redirect()->route('dosen.review-dokumen.index')
        //                      ->with('info', 'Dokumen ini sudah ditolak dan tidak dapat diproses lagi.');
        // }


        $dokumenProyekAkhir->load(['mahasiswa.user', 'jenisDokumen', 'reviewer']);
        return view('dosen.review_dokumen.proses', compact('dokumenProyekAkhir'));
    }

    /**
     * Update the review status and notes for the specified document.
     */
    public function updateReview(Request $request, DokumenProyekAkhir $dokumenProyekAkhir)
    {
        $dosen = Auth::user()->dosen;
        if (!$dosen || !$dokumenProyekAkhir->mahasiswa || $dokumenProyekAkhir->mahasiswa->dosen_pembimbing_id !== $dosen->id) {
            return redirect()->route('dosen.review-dokumen.index')->with('error', 'Aksi tidak diizinkan.');
        }

        // Prevent updating if the document was already approved and the new status is not 'approved'
        // This allows changing notes on an 'approved' document if desired, but not its 'approved' status.
        // If NO changes are allowed once 'approved', use:
        // if ($dokumenProyekAkhir->getOriginal('status_review') === 'approved')
        if ($dokumenProyekAkhir->getOriginal('status_review') === 'approved') {
             return redirect()->route('dosen.review-dokumen.index')
                              ->with('error', 'Dokumen yang sudah disetujui tidak dapat diubah statusnya lagi.');
        }
        // Optionally, add similar logic for 'rejected' if it's a final state
        // if ($dokumenProyekAkhir->getOriginal('status_review') === 'rejected') {
        //      return redirect()->route('dosen.review-dokumen.index')
        //                       ->with('error', 'Dokumen yang sudah ditolak tidak dapat diubah statusnya lagi.');
        // }

        $request->validate([
            'status_review' => ['required', Rule::in(['approved', 'revision_needed', 'rejected'])],
            'catatan_reviewer' => 'nullable|string|max:2000',
        ]);

        try {
            $dokumenProyekAkhir->status_review = $request->status_review;
            $dokumenProyekAkhir->catatan_reviewer = $request->catatan_reviewer;
            $dokumenProyekAkhir->reviewed_by = Auth::id();
            $dokumenProyekAkhir->reviewed_at = now();
            $dokumenProyekAkhir->save();

            Log::info("Dosen (User ID: ".Auth::id().") mereview dokumen (ID: {$dokumenProyekAkhir->id}) dengan status: {$request->status_review}");

            // TODO: Kirim notifikasi ke mahasiswa

            return redirect()->route('dosen.review-dokumen.index')->with('success', 'Review dokumen berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error("Error saat menyimpan review dokumen (ID: {$dokumenProyekAkhir->id}): " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan review dokumen. Silakan coba lagi.')->withInput();
        }
    }
}
