<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Report $report,
        public string $status,
        public ?string $note = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $target = $this->report->target_display;

        $info = match ($this->status) {
            'need_revision' => [
                'title' => 'Laporan Perlu Revisi',
                'message' => 'Laporan ' . $target . ' memerlukan revisi: ' . ($this->note ?? 'Mohon lengkapi catatan atau bukti laporan Anda.'),
                'icon' => 'exclamation-circle-fill',
                'badge_class' => 'danger',
            ],
            'pending_admin_review' => [
                'title' => 'Laporan Menunggu Persetujuan Admin',
                'message' => 'Laporan ' . $target . ' telah lolos verifikasi dan diteruskan ke admin.',
                'icon' => 'info-circle-fill',
                'badge_class' => 'info',
            ],
            'verified' => [
                'title' => 'Laporan Disetujui & Dipublikasikan',
                'message' => 'Laporan ' . $target . ' telah diverifikasi oleh admin dan kini tayang di pencarian publik.',
                'icon' => 'check-circle-fill',
                'badge_class' => 'success',
            ],
            'rejected' => [
                'title' => 'Laporan Ditolak',
                'message' => 'Laporan ' . $target . ' ditolak: ' . ($this->note ?? 'Laporan tidak memenuhi kriteria publikasi.'),
                'icon' => 'x-circle-fill',
                'badge_class' => 'secondary',
            ],
            default => [
                'title' => 'Status Laporan Diperbarui',
                'message' => 'Status laporan ' . $target . ' telah diperbarui.',
                'icon' => 'bell-fill',
                'badge_class' => 'primary',
            ],
        };

        return [
            'report_id' => $this->report->id,
            'reported_value' => $this->report->reported_value,
            'status' => $this->status,
            'title' => $info['title'],
            'message' => $info['message'],
            'url' => route('reports.show', $this->report->id),
            'icon' => $info['icon'],
            'badge_class' => $info['badge_class'],
        ];
    }
}
