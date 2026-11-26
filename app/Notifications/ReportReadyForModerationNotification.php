<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportReadyForModerationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Report $report
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'reported_value' => $this->report->reported_value,
            'title' => 'Laporan Siap Ditinjau Admin',
            'message' => 'Laporan nomor ' . $this->report->reported_value . ' telah lolos pemeriksaan verifikator dan menunggu moderasi akhir.',
            'url' => route('admin.reports.show', $this->report->id),
            'icon' => 'shield-check',
            'badge_class' => 'info',
        ];
    }
}
