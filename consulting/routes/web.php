<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
| Cached with `response-cache` style middleware kept deliberately simple:
| GET pages that render pure config content are safe to cache for a short
| window. Nothing behind auth and no route that reads the session is cached.
*/

Route::middleware('cache.page:600')->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/about', [PageController::class, 'about'])->name('about');

    Route::get('/services', [PageController::class, 'services'])->name('services');
    Route::get('/services/{slug}', [PageController::class, 'service'])
        ->where('slug', '[a-z0-9-]+')
        ->name('services.show');

    Route::get('/case-studies', [PageController::class, 'caseStudies'])->name('case-studies');
    Route::get('/case-studies/{slug}', [PageController::class, 'caseStudy'])
        ->where('slug', '[a-z0-9-]+')
        ->name('case-studies.show');

    Route::get('/legal/{document}', [PageController::class, 'legal'])
        ->where('document', '[a-z-]+')
        ->name('legal');
});

/*
| Forms. Not cached, and rate limited per IP.
| ProtectAgainstSpam adds the honeypot field check plus a minimum
| time-to-submit, which stops naive bots without a CAPTCHA.
*/

Route::get('/contact', [EnquiryController::class, 'contact'])->name('contact');
Route::get('/request-consultation', [EnquiryController::class, 'consultation'])->name('consultation');

Route::post('/enquiries/{type}', [EnquiryController::class, 'store'])
    ->middleware(['throttle:forms', ProtectAgainstSpam::class])
    ->where('type', 'contact|consultation')
    ->name('enquiries.store');

/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Private attachment download
|--------------------------------------------------------------------------
| Files never sit under public/. They are streamed through this route, which
| requires an authenticated panel user and an explicit policy check.
*/

Route::get('/enquiries/{enquiry}/attachment', AttachmentController::class)
    ->middleware(['auth', 'verified', 'throttle:downloads'])
    ->name('enquiries.attachment');
