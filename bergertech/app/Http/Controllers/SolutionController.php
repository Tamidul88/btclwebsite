<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SolutionController extends Controller
{
    public function index()
    {
        return view('pages.solutions-index', [
            'solutions' => config('solutions'),
        ]);
    }

    public function show(string $solution)
    {
        $data = config('solutions')[$solution] ?? null;

        if (! is_array($data)) {
            throw new NotFoundHttpException("Unknown solution [{$solution}].");
        }

        return view('pages.solution', [
            'slug' => $solution,
            'solution' => $data,
        ]);
    }
}
