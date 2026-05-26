<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id', 'student_session_id', 'answer',
        'status', 'score', 'teacher_feedback', 'graded_at',
        'student_reply', 'student_replied_at',
        'started_at', 'submitted_at',
    ];

    protected $casts = [
        'graded_at'          => 'datetime',
        'student_replied_at' => 'datetime',
        'started_at'         => 'datetime',
        'submitted_at'       => 'datetime',
    ];

    /**
     * Durasi pengerjaan dalam menit
     */
    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->started_at || !$this->submitted_at) return null;
        return (int) $this->started_at->diffInMinutes($this->submitted_at);
    }

    /**
     * Cek apakah waktu pengerjaan sudah habis
     */
    public function isTimeExpired(): bool
    {
        if (!$this->started_at) return false;
        $timeLimit = $this->assessment?->time_limit;
        if (!$timeLimit) return false;
        return $this->started_at->addMinutes($timeLimit)->isPast();
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function studentSession()
    {
        return $this->belongsTo(StudentSession::class);
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }
}
