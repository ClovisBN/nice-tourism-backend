<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointOfInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_point',
        'coordinate',
        'details',
        'image_path', // Ajouter ce champ
    ];

    protected $casts = [
        'coordinate' => 'array',
        'details' => 'array',
    ];
}
