<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CertificateTemplateController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\CertificateController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('events', EventController::class);
    
    Route::prefix('events/{event}')->group(function () {
        Route::resource('certificate-templates', CertificateTemplateController::class);
        
        Route::resource('participants', ParticipantController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::get('participants/import', [ParticipantController::class, 'import'])->name('participants.import');
        Route::post('participants/import', [ParticipantController::class, 'processImport'])->name('participants.process-import');
        
        Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::post('certificates/generate', [CertificateController::class, 'generate'])->name('certificates.generate');
        Route::post('certificates/send', [CertificateController::class, 'send'])->name('certificates.send');
        Route::get('certificates/download-all', [CertificateController::class, 'downloadAll'])->name('certificates.download-all');
    });
    
    Route::get('certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
});

// Auth routes (simplified - in production use Laravel Breeze or Jetstream)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (auth()->attempt($credentials)) {
        return redirect()->intended('/dashboard');
    }
    
    return back()->with('error', 'Invalid credentials');
})->name('login.post');

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');