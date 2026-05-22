<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'slug', 'is_active'];

    protected static function booted(): void
    {
        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title) . '-' . Str::random(6);
            }
        });
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class)->orderBy('order');
    }

    public function studentSessions()
    {
        return $this->hasMany(StudentSession::class);
    }
}
