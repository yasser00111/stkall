<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_session_id', 'token', 'type',
        'material_id', 'is_used', 'expires_at',
    ];

    protected $casts = [
        'is_used'    => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($token) {
            if (empty($token->token)) {
                do {
                    $code = strtoupper(Str::random(8));
                } while (self::where('token', $code)->exists());
                $token->token = $code;
            }
        });
    }

    public function studentSession()
    {
        return $this->belongsTo(StudentSession::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function isValid(): bool
    {
        if ($this->is_used) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }
}
