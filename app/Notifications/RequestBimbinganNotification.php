<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class RequestBimbinganNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $requestBimbingan;
    public $type; // 'to_dosen' atau 'to_mahasiswa'

    public function __construct($requestBimbingan, $type = 'to_dosen')
    {
        $this->requestBimbingan = $requestBimbingan;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database']; // Bisa tambahkan 'mail' jika ingin email
    }

    public function toDatabase($notifiable)
    {
        if ($this->type === 'to_dosen') {
            return [
                'title' => 'Pengajuan Bimbingan Baru',
                'message' => 'Ada pengajuan bimbingan baru dari mahasiswa: ' . $this->requestBimbingan->mahasiswa->user->name,
                'request_bimbingan_id' => $this->requestBimbingan->id,
                'from' => $this->requestBimbingan->mahasiswa->user->name,
                'role' => 'mahasiswa',
            ];
        } else {
            return [
                'title' => 'Respon Bimbingan',
                'message' => 'Dosen telah memberikan respon pada pengajuan bimbingan Anda.',
                'request_bimbingan_id' => $this->requestBimbingan->id,
                'from' => $this->requestBimbingan->dosen->user->name,
                'role' => 'dosen',
            ];
        }
    }
}
