<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ParticipantsImport;

class ParticipantController extends Controller
{
    public function index(Event $event)
    {
        $this->authorize('view', $event);
        
        $participants = $event->participants()->paginate(15);
        
        return view('participants.index', compact('event', 'participants'));
    }

    public function create(Event $event)
    {
        $this->authorize('update', $event);
        
        return view('participants.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'data' => 'nullable|array',
        ]);

        $event->participants()->create($request->only(['name', 'email', 'data']));

        return redirect()->route('participants.index', $event)
            ->with('success', 'Participant added successfully!');
    }

    public function import(Event $event)
    {
        $this->authorize('update', $event);
        
        return view('participants.import', compact('event'));
    }

    public function processImport(Request $request, Event $event)
    {
        $this->authorize('update', $event);
        
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            Excel::import(new ParticipantsImport($event), $request->file('file'));
            
            return redirect()->route('participants.index', $event)
                ->with('success', 'Participants imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function destroy(Event $event, Participant $participant)
    {
        $this->authorize('update', $event);
        
        $participant->delete();

        return redirect()->route('participants.index', $event)
            ->with('success', 'Participant deleted successfully!');
    }
}