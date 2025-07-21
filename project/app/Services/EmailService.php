<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendCertificate(Certificate $certificate)
    {
        try {
            $participant = $certificate->participant;
            $event = $certificate->event;
            
            $subject = "Your Certificate for {$event->name}";
            $body = "Dear {$participant->name},\n\nPlease find your certificate attached.\n\nBest regards,\n{$event->name} Team";

            Mail::send([], [], function ($message) use ($certificate, $participant, $subject, $body) {
                $message->to($participant->email, $participant->name)
                    ->subject($subject)
                    ->setBody($body)
                    ->attach(storage_path('app/public/' . $certificate->pdf_path));
            });

            // Update certificate status
            $certificate->update([
                'email_sent' => true,
                'email_sent_at' => now(),
            ]);

            // Log email
            EmailLog::create([
                'certificate_id' => $certificate->id,
                'recipient_email' => $participant->email,
                'subject' => $subject,
                'body' => $body,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            // Log error
            EmailLog::create([
                'certificate_id' => $certificate->id,
                'recipient_email' => $certificate->participant->email,
                'subject' => $subject ?? 'Certificate Email',
                'body' => $body ?? '',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}