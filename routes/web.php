<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return view('chat');
});

// Chat routes
Route::prefix('api/chat')->group(function () {
    Route::post('/send', [ChatController::class, 'sendMessage'])
        ->middleware('chat.rate_limit')
        ->name('chat.send');
    Route::get('/messages/{channel?}', [ChatController::class, 'getMessages'])->name('chat.messages');
});

// Legacy route for backward compatibility
Route::post('/send-message', [ChatController::class, 'sendMessage'])
    ->middleware('chat.rate_limit');
