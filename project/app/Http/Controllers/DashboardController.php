<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Certificate;
use App\Models\Participant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $totalEvents = Event::where('user_id', $user->id)->count();
        $totalParticipants = Participant::whereHas('event', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        $totalCertificates = Certificate::whereHas('event', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        $emailsSent = Certificate::whereHas('event', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('email_sent', true)->count();

        $recentEvents = Event::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalEvents',
            'totalParticipants', 
            'totalCertificates',
            'emailsSent',
            'recentEvents'
        ));
    }
}