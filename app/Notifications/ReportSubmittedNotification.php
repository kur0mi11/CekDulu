<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Report $report,
        public bool $isRevision = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->isRevision ? 'Laporan Selesai Direvisi' : 'Laporan Baru Masuk';
        $message = $this->isRevision
            ? 'Laporan nomor ' . $this->report->reported_value . ' telah diperbaiki oleh pelapor dan siap diverifikasi ulang.'
            : 'Laporan baru nomor ' . $this->report->reported_value . ' telah dikirim dan menunggu antrean pemeriksaan.';

        return [
            'report_id' => $this->report->id,
            'reported_value' => $this->report->reported_value,
            'is_revision' => $this->isRevision,
            'title' => $title,
            'message' => $message,
            'url' => route('verifier.reports.show', $this->report->id),
            'icon' => $this->isRevision ? 'arrow-clockwise' : 'file-earmark-plus-fill',
            'badge_class' => $this->isRevision ? 'warning' : 'primary',
        ];
    }
}
