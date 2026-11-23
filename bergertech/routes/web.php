<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SolutionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/resources', [PageController::class, 'resources'])->name('resources');

Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
Route::get('/insights/{insight}', [InsightController::class, 'show'])->name('insights.show');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/solutions', [SolutionController::class, 'index'])->name('solutions.index');
Route::get('/solutions/{solution}', [SolutionController::class, 'show'])->name('solutions.show');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact.store');

Route::get('/appointment', [AppointmentController::class, 'create'])->name('appointment');
Route::post('/appointment', [AppointmentController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('appointment.store');
