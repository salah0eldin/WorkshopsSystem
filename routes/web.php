<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\WorkshopDataController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\GettingInController;

Route::get('/', function () {
    return view(view: 'welcome');
})->name('welcome');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware([AdminMiddleware::class, 'verified'])->name('dashboard');

Route::middleware(AdminMiddleware::class)->group(function () {
    Route::get('/dashboard', [WorkshopController::class, 'index'])->name('dashboard');
    Route::resource('workshops', controller: WorkshopController::class);      
    Route::resource('students', controller: StudentsController::class);   
    Route::post('/workshops/{workshop}/sessions', [WorkshopDataController::class, 'storeSessions'])->name('workshops.storeSessions');
    Route::post('/workshops/{id}/save-analysis', [WorkshopDataController::class, 'saveAnalysis'])->name('workshops.saveAnalysis');
    Route::get('/workshop-data', [WorkshopDataController::class, 'getFirstData'])->name('workshop-data.first');

    Route::get('/getting-in', [GettingInController::class, 'index'])->name('getting-in.index');
    Route::post('/enroll', [GettingInController::class, 'store'])->name('enroll.store');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
