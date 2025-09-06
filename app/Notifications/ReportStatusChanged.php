<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReportStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;
    protected $newStatus;
    protected $description;

    /**
     * Create a new notification instance.
     */
    public function __construct($report, $newStatus, $description = null)
    {
        $this->report = $report;
        $this->newStatus = $newStatus;
        $this->description = $description;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/');
        if (method_exists($this->report, 'getUrl')) {
            $url = $this->report->getUrl();
        } else if (property_exists($this->report, 'id')) {
            $url = url('/admin/report/' . $this->report->id);
        }

        $mail = (new MailMessage)
            ->subject('Status Laporan Anda Berubah')
            ->greeting('Halo,')
            ->line('Status laporan dengan judul: "' . $this->report->title . '" telah berubah.')
            ->line('Status terbaru: ' . ucfirst(str_replace('_', ' ', $this->newStatus)));
        if ($this->description) {
            $mail->line('Deskripsi: ' . $this->description);
        }
        $mail->action('Lihat Detail Laporan', $url)
            ->line('Terima kasih telah menggunakan layanan pengaduan masyarakat.');
        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
