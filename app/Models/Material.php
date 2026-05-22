<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'title', 'content', 'video_url', 'order', 'is_active'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function assessment()
    {
        return $this->hasOne(Assessment::class);
    }

    public function resumes()
    {
        return $this->hasMany(Resume::class);
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    /**
     * Extract YouTube video embed ID from URL
     */
    public function getYoutubeIdAttribute(): ?string
    {
        if (!$this->video_url) return null;

        preg_match(
            '/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            $this->video_url,
            $matches
        );

        return $matches[1] ?? null;
    }
}
