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
    ];

    protected $casts = [
        'graded_at'          => 'datetime',
        'student_replied_at' => 'datetime',
    ];

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
