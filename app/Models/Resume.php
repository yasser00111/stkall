<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_session_id', 'material_id', 'content',
        'status', 'teacher_feedback', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function studentSession()
    {
        return $this->belongsTo(StudentSession::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
