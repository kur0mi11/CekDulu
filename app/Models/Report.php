<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'reported_type',
        'reported_value',
        'chronology',
        'evidence_file',
        'status',
        'verifier_id',
        'admin_id',
        'revision_note',
    ];


    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'              => 'Menunggu Pemeriksaan',
            'need_revision'        => 'Perlu Revisi',
            'pending_admin_review' => 'Menunggu Peninjauan',
            'verified'             => 'Terverifikasi',
            'rejected'             => 'Ditolak',
            default                => (string) $this->status,
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'pending'              => 'bg-warning text-dark',
            'need_revision'        => 'bg-danger',
            'pending_admin_review' => 'bg-info text-dark',
            'verified'             => 'bg-success',
            default                => 'bg-secondary',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}