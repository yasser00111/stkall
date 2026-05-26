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
        'student_reply', 'student_replied_at',
        'file_path', 'file_name', 'file_type', 'video_url',
    ];

    protected $casts = [
        'approved_at'        => 'datetime',
        'student_replied_at' => 'datetime',
    ];

    /**
     * Cek apakah resume punya file upload
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }

    /**
     * Cek apakah file adalah PDF
     */
    public function isPdf(): bool
    {
        return $this->file_type === 'pdf' ||
               str_ends_with(strtolower($this->file_name ?? ''), '.pdf');
    }

    /**
     * Cek apakah file adalah gambar
     */
    public function isImage(): bool
    {
        return in_array($this->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * URL publik file
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /**
     * Ambil YouTube video ID dari URL
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
