<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketPurchaseController;
use App\Http\Controllers\UserTicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EventController::class, 'index'])->name('events.index');

Route::get('/dashboard', function () {
    return redirect()->route('events.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::middleware('auth')->group(function () {
    Route::get('/events/{event}/purchase', [TicketPurchaseController::class, 'create'])->name('events.purchase.create');
    Route::post('/events/{event}/purchase', [TicketPurchaseController::class, 'store'])->name('events.purchase.store');

    Route::resource('tickets', UserTicketController::class)
        ->only(['index'])
        ->names(['index' => 'tickets.my-tickets',
    ]);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('admin/events/create', [AdminEventController::class, 'create'])->name('admin.events.create');
    Route::post('admin/events', [AdminEventController::class, 'store'])->name('admin.events.store');

    Route::get('admin/events/{event}/edit', [AdminEventController::class, 'edit'])->name('admin.events.edit');
    Route::patch('admin/events/{event}', [AdminEventController::class, 'update'])->name('admin.events.update');

    Route::delete('/admin/events/{event}', [AdminController::class, 'destroy'])->name('admin.events.destroy');
});

require __DIR__.'/auth.php';
