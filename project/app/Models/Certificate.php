<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'event_id',
        'participant_id',
        'template_id',
        'pdf_path',
        'jpg_path',
        'generated_at',
        'email_sent',
        'email_sent_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }
}