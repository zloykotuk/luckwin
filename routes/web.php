<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\PlayLuckyAction;
use App\Http\Controllers\RegisterAction;
use App\Http\Controllers\ShowHistoryAction;
use App\Http\Controllers\ShowLinkAction;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Home')->name('home');

Route::post('/register', RegisterAction::class)->name('register.store');

Route::prefix('/a/{link}')->name('links.')->group(function () {
    Route::get('/', ShowLinkAction::class)->name('show');
    Route::patch('/regenerate', [LinkController::class, 'regenerate'])->name('regenerate');
    Route::delete('/delete', [LinkController::class, 'destroy'])->name('destroy');
    Route::post('/lucky', PlayLuckyAction::class)->name('lucky.store');
    Route::get('/history', ShowHistoryAction::class)->name('history.index');
});
