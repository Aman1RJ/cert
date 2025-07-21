<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    public function index(Event $event)
    {
        $this->authorize('view', $event);
        
        $templates = $event->certificateTemplates()->paginate(10);
        
        return view('certificate-templates.index', compact('event', 'templates'));
    }

    public function create(Event $event)
    {
        $this->authorize('view', $event);
        
        return view('certificate-templates.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'template_content' => 'required|string',
            'placeholders' => 'required|array',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'template_content', 'placeholders']);
        $data['event_id'] = $event->id;

        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('backgrounds', 'public');
            $data['background_image'] = $path;
        }

        CertificateTemplate::create($data);

        return redirect()->route('certificate-templates.index', $event)
            ->with('success', 'Certificate template created successfully!');
    }

    public function show(Event $event, CertificateTemplate $certificateTemplate)
    {
        $this->authorize('view', $event);
        
        return view('certificate-templates.show', compact('event', 'certificateTemplate'));
    }

    public function edit(Event $event, CertificateTemplate $certificateTemplate)
    {
        $this->authorize('update', $event);
        
        return view('certificate-templates.edit', compact('event', 'certificateTemplate'));
    }

    public function update(Request $request, Event $event, CertificateTemplate $certificateTemplate)
    {
        $this->authorize('update', $event);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'template_content' => 'required|string',
            'placeholders' => 'required|array',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'template_content', 'placeholders']);

        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('backgrounds', 'public');
            $data['background_image'] = $path;
        }

        $certificateTemplate->update($data);

        return redirect()->route('certificate-templates.show', [$event, $certificateTemplate])
            ->with('success', 'Certificate template updated successfully!');
    }

    public function destroy(Event $event, CertificateTemplate $certificateTemplate)
    {
        $this->authorize('update', $event);
        
        $certificateTemplate->delete();

        return redirect()->route('certificate-templates.index', $event)
            ->with('success', 'Certificate template deleted successfully!');
    }
}