<?php

namespace App\Models;

use App\Support\Bank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'reported_type',
        'bank_name',
        'reported_value',
        'account_holder',
        'chronology',
        'evidence_file',
        'status',
        'verifier_id',
        'admin_id',
        'revision_note',
        'public_summary',
    ];

    public function getTargetDisplayAttribute(): string
    {
        if ($this->reported_type === 'bank_account') {
            $bank = Bank::label($this->bank_name);
            $holder = $this->account_holder ? ' (a.n. ' . $this->account_holder . ')' : '';
            return "{$bank} - {$this->reported_value}{$holder}";
        }

        return (string) $this->reported_value;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->reported_type) {
            'bank_account' => 'Rekening Bank / E-Wallet',
            'phone'        => 'Nomor Telepon',
            default        => ucfirst((string) $this->reported_type),
        };
    }


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