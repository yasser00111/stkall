<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = ['material_id', 'title', 'instructions', 'is_active', 'time_limit', 'deadline'];

    protected $casts = [
        'deadline'   => 'datetime',
        'time_limit' => 'integer',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function answers()
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    /**
     * Cek apakah asesmen masih dalam periode yang valid (deadline belum lewat)
     */
    public function isOpen(): bool
    {
        if ($this->deadline && $this->deadline->isPast()) {
            return false;
        }
        return true;
    }

    /**
     * Format batas waktu untuk ditampilkan
     */
    public function getTimeLimitLabelAttribute(): string
    {
        if (!$this->time_limit) return 'Tidak terbatas';
        $h = intdiv($this->time_limit, 60);
        $m = $this->time_limit % 60;
        return $h > 0 ? "{$h} jam {$m} menit" : "{$m} menit";
    }
}
