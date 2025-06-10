<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';
    protected $fillable = [
        'user_id',
        'nim',
        'prodi_id',
        'angkatan',
        'nomor_kelompok',
        'dosen_pembimbing_id',
        'judul_proyek_akhir',
        'status_proyek_akhir',
    ];
    protected $casts = [
        'angkatan' => 'integer',
    ];

    // --- RELASI LAMA (TIDAK ADA PERUBAHAN) ---
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function dosenPembimbing()
    {
        return $this->belongsTo(Dosen::class, 'dosen_pembimbing_id');
    }

    public function requestJudul()
    {
        return $this->hasMany(RequestJudul::class);
    }

    public function requestBimbingan()
    {
        return $this->hasMany(RequestBimbingan::class);
    }

    public function historyBimbingan()
    {
        return $this->hasMany(HistoryBimbingan::class);
    }

    public function dokumenProyekAkhir()
    {
        return $this->hasMany(DokumenProyekAkhir::class);
    }

    public function logActivities()
    {
        return $this->hasMany(LogActivity::class, 'mahasiswa_terkait_id');
    }

    // --- PERBAIKAN RELATIONSHIP TEMAN SEKELOMPOK ---

    /**
     * Mendapatkan semua mahasiswa yang berada dalam kelompok yang sama
     * berdasarkan prodi, angkatan, dan nomor kelompok yang sama.
     * EXCLUDE mahasiswa yang sedang login (berdasarkan id)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function temanSeKelompok()
    {
        // PERBAIKAN 1: Menggunakan hasMany dengan scope yang benar
        return $this->hasMany(Mahasiswa::class, 'nomor_kelompok', 'nomor_kelompok')
            ->where('prodi_id', $this->prodi_id)
            ->where('angkatan', $this->angkatan)
            ->where('id', '!=', $this->id); // Exclude diri sendiri
    }

    /**
     * Alternative method: Menggunakan scopeQuery untuk lebih fleksibel
     * Gunakan ini jika method di atas masih bermasalah
     */
    public function temanSeKelompokAlt()
    {
        // Safety check
        if (is_null($this->nomor_kelompok) || is_null($this->prodi_id) || is_null($this->angkatan)) {
            // Return empty relationship
            return $this->hasMany(Mahasiswa::class)->whereRaw('1 = 0');
        }

        return $this->hasMany(Mahasiswa::class, 'nomor_kelompok', 'nomor_kelompok')
            ->where('prodi_id', $this->prodi_id)
            ->where('angkatan', $this->angkatan)
            ->where('id', '!=', $this->id);
    }

    /**
     * Method untuk mendapatkan semua anggota kelompok (termasuk diri sendiri)
     * Berguna untuk beberapa use case
     */
    public function semuaAnggotaKelompok()
    {
        // Safety check
        if (is_null($this->nomor_kelompok) || is_null($this->prodi_id) || is_null($this->angkatan)) {
            // Return collection berisi hanya diri sendiri
            return collect([$this]);
        }

        return Mahasiswa::where('nomor_kelompok', $this->nomor_kelompok)
            ->where('prodi_id', $this->prodi_id)
            ->where('angkatan', $this->angkatan)
            ->get();
    }

    /**
     * Scope untuk mencari mahasiswa sekelompok
     * Bisa digunakan sebagai alternative method
     */
    public function scopeSekelompokDengan($query, Mahasiswa $mahasiswa)
    {
        return $query->where('nomor_kelompok', $mahasiswa->nomor_kelompok)
            ->where('prodi_id', $mahasiswa->prodi_id)
            ->where('angkatan', $mahasiswa->angkatan)
            ->where('id', '!=', $mahasiswa->id);
    }
}
