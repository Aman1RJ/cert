<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'event_date',
        'status',
        'settings',
    ];

    protected $casts = [
        'event_date' => 'date',
        'settings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function certificateTemplates()
    {
        return $this->hasMany(CertificateTemplate::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}