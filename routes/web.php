<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('customers', [App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [App\Http\Controllers\CustomerController::class, 'show'])->name('customers.show');
    Route::put('customers/{customer}', [App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');

    Route::get('ledgers', [App\Http\Controllers\LedgerController::class, 'index'])->name('ledgers.index');
    Route::post('ledgers', [App\Http\Controllers\LedgerController::class, 'store'])->name('ledgers.store');
    Route::put('ledgers/{ledger}', [App\Http\Controllers\LedgerController::class, 'update'])->name('ledgers.update');
    Route::delete('ledgers/{ledger}', [App\Http\Controllers\LedgerController::class, 'destroy'])->name('ledgers.destroy');

    Route::get('sheets', [App\Http\Controllers\SheetController::class, 'index'])->name('sheets.index');
    Route::post('sheets', [App\Http\Controllers\SheetController::class, 'store'])->name('sheets.store');
    Route::put('sheets/{sheet}', [App\Http\Controllers\SheetController::class, 'update'])->name('sheets.update');
    Route::delete('sheets/{sheet}', [App\Http\Controllers\SheetController::class, 'destroy'])->name('sheets.destroy');

    Route::post('irrigations', [App\Http\Controllers\IrrigationController::class, 'store']);
    Route::put('irrigations/{irrigation}', [App\Http\Controllers\IrrigationController::class, 'update']);

    Route::post('maintenances', [App\Http\Controllers\MaintenanceController::class, 'store']);
    Route::put('maintenances/{maintenance}', [App\Http\Controllers\MaintenanceController::class, 'update']);

    Route::post('schedules', [App\Http\Controllers\ScheduleController::class, 'store']);
    Route::put('schedules/{schedule}', [App\Http\Controllers\ScheduleController::class, 'update']);

    Route::get('service-calendar', [App\Http\Controllers\ServiceCalendarController::class, 'index'])->name('service-calendar.index');
    Route::put('service-calendar/{schedule}', [App\Http\Controllers\ServiceCalendarController::class, 'updateEvent'])->name('service-calendar.update');

    Route::get('area-groups', [\App\Http\Controllers\AreaGroupController::class, 'index'])->name('area-groups.index');
    Route::post('area-groups', [\App\Http\Controllers\AreaGroupController::class, 'store'])->name('area-groups.store');
    Route::put('area-groups/{areaGroup}', [\App\Http\Controllers\AreaGroupController::class, 'update'])->name('area-groups.update');
    Route::delete('area-groups/{areaGroup}', [\App\Http\Controllers\AreaGroupController::class, 'destroy'])->name('area-groups.destroy');
});

require __DIR__.'/settings.php';
