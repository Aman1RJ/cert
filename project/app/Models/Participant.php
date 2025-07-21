<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'email',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}