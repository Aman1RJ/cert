<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'template_content',
        'placeholders',
        'background_image',
        'settings',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'settings' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'template_id');
    }
}