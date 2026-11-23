<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home');
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function services(): View
    {
        return view('pages.services');
    }

    public function service(string $slug): View
    {
        $service = config("site.services.{$slug}");

        if (! is_array($service)) {
            throw new NotFoundHttpException;
        }

        return view('pages.service', ['slug' => $slug, 'service' => $service]);
    }

    public function caseStudies(): View
    {
        return view('pages.case-studies');
    }

    public function caseStudy(string $slug): View
    {
        $study = config("site.case_studies.{$slug}");

        if (! is_array($study)) {
            throw new NotFoundHttpException;
        }

        return view('pages.case-study', ['slug' => $slug, 'study' => $study]);
    }

    public function legal(string $document): View
    {
        // Allowlist: never resolve a view name straight from the URL.
        $allowed = ['privacy', 'terms', 'cookies'];

        if (! in_array($document, $allowed, true)) {
            throw new NotFoundHttpException;
        }

        return view("pages.legal.{$document}");
    }
}
