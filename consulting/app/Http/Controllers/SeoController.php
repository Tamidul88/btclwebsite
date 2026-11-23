<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0', 'freq' => 'weekly'],
            ['loc' => route('about'), 'priority' => '0.7', 'freq' => 'monthly'],
            ['loc' => route('services'), 'priority' => '0.9', 'freq' => 'monthly'],
            ['loc' => route('case-studies'), 'priority' => '0.8', 'freq' => 'monthly'],
            ['loc' => route('contact'), 'priority' => '0.8', 'freq' => 'yearly'],
            ['loc' => route('consultation'), 'priority' => '0.8', 'freq' => 'yearly'],
        ]);

        foreach (array_keys(config('site.services')) as $slug) {
            $urls->push(['loc' => route('services.show', $slug), 'priority' => '0.8', 'freq' => 'monthly']);
        }

        foreach (array_keys(config('site.case_studies')) as $slug) {
            $urls->push(['loc' => route('case-studies.show', $slug), 'priority' => '0.6', 'freq' => 'yearly']);
        }

        foreach (['privacy', 'terms', 'cookies'] as $doc) {
            $urls->push(['loc' => route('legal', $doc), 'priority' => '0.3', 'freq' => 'yearly']);
        }

        return response()
            ->view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            // The panel is not secret, but there is no reason to publish it.
            'Disallow: /'.config('security.admin_path'),
            'Disallow: /enquiries/',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines))
            ->header('Content-Type', 'text/plain');
    }
}
