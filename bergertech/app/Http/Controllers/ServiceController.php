<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceController extends Controller
{
    public function index()
    {
        return view('pages.services-index', [
            'services' => config('services_content'),
        ]);
    }

    public function show(string $service)
    {
        $data = config('services_content')[$service] ?? null;

        if (! is_array($data)) {
            throw new NotFoundHttpException("Unknown service [{$service}].");
        }

        return view('pages.service', [
            'slug' => $service,
            'service' => $data,
        ]);
    }
}
