<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class HistoryBimbinganNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $historyBimbingan;
    public $type; // 'to_dosen' atau 'to_mahasiswa'

    public function __construct($historyBimbingan, $type = 'to_dosen')
    {
        $this->historyBimbingan = $historyBimbingan;
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
                'title' => 'Update History Bimbingan',
                'message' => 'Mahasiswa telah mengupdate history bimbingan.',
                'history_bimbingan_id' => $this->historyBimbingan->id,
                'from' => $this->historyBimbingan->mahasiswa->user->name,
                'role' => 'mahasiswa',
            ];
        } else {
            return [
                'title' => 'Update History Bimbingan',
                'message' => 'Dosen telah mengupdate history bimbingan Anda.',
                'history_bimbingan_id' => $this->historyBimbingan->id,
                'from' => $this->historyBimbingan->dosen->user->name,
                'role' => 'dosen',
            ];
        }
    }
}
