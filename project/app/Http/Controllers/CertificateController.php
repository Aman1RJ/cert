<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Certificate;
use App\Services\CertificateService;
use App\Services\EmailService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    protected $certificateService;
    protected $emailService;

    public function __construct(CertificateService $certificateService, EmailService $emailService)
    {
        $this->certificateService = $certificateService;
        $this->emailService = $emailService;
    }

    public function index(Event $event)
    {
        $this->authorize('view', $event);
        
        $certificates = $event->certificates()->with(['participant', 'template'])->paginate(15);
        
        return view('certificates.index', compact('event', 'certificates'));
    }

    public function generate(Event $event)
    {
        $this->authorize('update', $event);
        
        $template = $event->certificateTemplates()->first();
        $participants = $event->participants;

        if (!$template) {
            return back()->with('error', 'No certificate template found for this event.');
        }

        if ($participants->isEmpty()) {
            return back()->with('error', 'No participants found for this event.');
        }

        try {
            $generated = 0;
            foreach ($participants as $participant) {
                $this->certificateService->generate($event, $participant, $template);
                $generated++;
            }

            return redirect()->route('certificates.index', $event)
                ->with('success', "Generated {$generated} certificates successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Certificate generation failed: ' . $e->getMessage());
        }
    }

    public function send(Event $event)
    {
        $this->authorize('update', $event);
        
        $certificates = $event->certificates()->where('email_sent', false)->get();

        if ($certificates->isEmpty()) {
            return back()->with('error', 'No certificates to send or all certificates have already been sent.');
        }

        try {
            $sent = 0;
            foreach ($certificates as $certificate) {
                if ($this->emailService->sendCertificate($certificate)) {
                    $sent++;
                }
            }

            return redirect()->route('certificates.index', $event)
                ->with('success', "Sent {$sent} certificates successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Email sending failed: ' . $e->getMessage());
        }
    }

    public function download(Certificate $certificate)
    {
        $this->authorize('view', $certificate->event);
        
        if (!file_exists(storage_path('app/public/' . $certificate->pdf_path))) {
            return back()->with('error', 'Certificate file not found.');
        }

        return response()->download(
            storage_path('app/public/' . $certificate->pdf_path),
            $certificate->participant->name . '_certificate.pdf'
        );
    }

    public function downloadAll(Event $event)
    {
        $this->authorize('view', $event);
        
        $zipPath = $this->certificateService->createZipArchive($event);
        
        return response()->download($zipPath)->deleteFileAfterSend();
    }
}