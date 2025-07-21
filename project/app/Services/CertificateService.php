<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Participant;
use App\Models\CertificateTemplate;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use ZipArchive;

class CertificateService
{
    public function generate(Event $event, Participant $participant, CertificateTemplate $template)
    {
        $certificateId = $this->generateCertificateId();
        
        // Replace placeholders in template
        $content = $this->replacePlaceholders(
            $template->template_content,
            $template->placeholders,
            $participant,
            $event,
            $certificateId
        );

        // Generate PDF
        $pdf = Pdf::loadHTML($content);
        $pdfPath = 'certificates/pdf/' . $certificateId . '.pdf';
        $pdf->save(storage_path('app/public/' . $pdfPath));

        // Generate JPG
        $jpgPath = 'certificates/jpg/' . $certificateId . '.jpg';
        $this->generateJpgFromPdf(storage_path('app/public/' . $pdfPath), storage_path('app/public/' . $jpgPath));

        // Save to database
        return Certificate::create([
            'certificate_id' => $certificateId,
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'template_id' => $template->id,
            'pdf_path' => $pdfPath,
            'jpg_path' => $jpgPath,
            'generated_at' => now(),
        ]);
    }

    private function generateCertificateId()
    {
        return 'CERT-' . strtoupper(Str::random(10));
    }

    private function replacePlaceholders($content, $placeholders, $participant, $event, $certificateId)
    {
        $replacements = [
            'participant_name' => $participant->name,
            'participant_email' => $participant->email,
            'event_name' => $event->name,
            'event_date' => $event->event_date->format('F j, Y'),
            'certificate_id' => $certificateId,
            'date_issued' => now()->format('F j, Y'),
        ];

        // Add custom participant data
        if ($participant->data) {
            $replacements = array_merge($replacements, $participant->data);
        }

        foreach ($replacements as $key => $value) {
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
        }

        return $content;
    }

    private function generateJpgFromPdf($pdfPath, $jpgPath)
    {
        // For now, create a simple placeholder image
        // In production, you'd use ImageMagick or similar to convert PDF to JPG
        $img = Image::canvas(800, 600, '#ffffff');
        $img->text('Certificate Generated', 400, 300, function($font) {
            $font->size(24);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });
        $img->save($jpgPath, 85);
    }

    public function createZipArchive(Event $event)
    {
        $zip = new ZipArchive();
        $zipPath = storage_path('app/temp/certificates_' . $event->id . '_' . time() . '.zip');
        
        if (!is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($event->certificates as $certificate) {
                if (file_exists(storage_path('app/public/' . $certificate->pdf_path))) {
                    $zip->addFile(
                        storage_path('app/public/' . $certificate->pdf_path),
                        $certificate->participant->name . '_certificate.pdf'
                    );
                }
            }
            $zip->close();
        }

        return $zipPath;
    }
}