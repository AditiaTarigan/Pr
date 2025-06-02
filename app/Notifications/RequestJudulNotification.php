<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class RequestJudulNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $requestJudul;
    public $type; // 'to_dosen' atau 'to_mahasiswa'

    public function __construct($requestJudul, $type = 'to_dosen')
    {
        $this->requestJudul = $requestJudul;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        if ($this->type === 'to_dosen') {
            return [
                'title' => 'Pengajuan Judul Baru',
                'message' => 'Ada pengajuan judul baru dari mahasiswa: ' . $this->requestJudul->mahasiswa->user->name,
                'request_judul_id' => $this->requestJudul->id,
                'from' => $this->requestJudul->mahasiswa->user->name,
                'role' => 'mahasiswa',
            ];
        } else {
            return [
                'title' => 'Respon Judul',
                'message' => 'Dosen telah memberikan respon pada pengajuan judul Anda.',
                'request_judul_id' => $this->requestJudul->id,
                'from' => $this->requestJudul->dosenTujuan->user->name,
                'role' => 'dosen',
            ];
        }
    }
}
