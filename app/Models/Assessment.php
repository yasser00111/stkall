<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = ['material_id', 'title', 'instructions', 'is_active'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function answers()
    {
        return $this->hasMany(AssessmentAnswer::class);
    }
}
