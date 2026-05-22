<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StudentSession extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'name', 'class', 'session_token', 'current_material_order'];

    protected static function booted(): void
    {
        static::creating(function ($session) {
            if (empty($session->session_token)) {
                $session->session_token = Str::upper(Str::random(12));
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function resumes()
    {
        return $this->hasMany(Resume::class);
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    public function assessmentAnswers()
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    /**
     * Cek apakah siswa sudah submit resume untuk material tertentu
     */
    public function hasResumeFor(int $materialId): bool
    {
        return $this->resumes()->where('material_id', $materialId)->exists();
    }

    /**
     * Cek apakah siswa sudah mengerjakan assessment untuk material tertentu
     */
    public function hasAnsweredAssessment(int $assessmentId): bool
    {
        return $this->assessmentAnswers()->where('assessment_id', $assessmentId)->exists();
    }
}
