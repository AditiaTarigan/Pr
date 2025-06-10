<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User; // Pastikan Anda sudah punya model User
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /**
     * Menampilkan form untuk meminta link reset password.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Mengirim link reset password ke email pengguna.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Kami tidak dapat menemukan pengguna dengan alamat email tersebut.');
        }

        // Hapus token lama jika ada
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Buat token baru
        $token = Str::random(60);

        // Simpan token ke database
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // Buat link reset
        $resetLink = route('password.reset', ['token' => $token, 'email' => $request->email]);

        // Kirim email (menggunakan Mailtrap)
        Mail::send('auth.passwords.email_template', ['link' => $resetLink], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password Akun Anda');
        });

        return back()->with('status', 'Kami telah mengirimkan link reset password ke email Anda!');
    }

    /**
     * Menampilkan form untuk mereset password.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Memproses reset password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        // Cari request reset
        $resetRequest = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        // Cek jika token tidak valid atau tidak ada
        if (!$resetRequest) {
            return back()->withInput()->with('error', 'Token reset password tidak valid.');
        }

        // Cek jika token sudah expired (misal: lebih dari 60 menit)
        if (Carbon::parse($resetRequest->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return redirect()->route('password.request')->with('error', 'Token reset password telah kedaluwarsa. Silakan minta yang baru.');
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token dari database setelah digunakan
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect('/login')->with('status', 'Password Anda telah berhasil direset! Silakan login.');
    }
}
