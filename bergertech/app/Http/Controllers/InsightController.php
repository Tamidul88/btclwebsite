<?php

namespace App\Http\Controllers;

use App\InsightLibrary;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InsightController extends Controller
{
    public function index(InsightLibrary $library): View
    {
        return view('pages.insights-index', [
            'insights' => $library->all(),
        ]);
    }

    public function show(InsightLibrary $library, string $insight): View
    {
        $data = $library->find($insight);

        if ($data === null) {
            throw new NotFoundHttpException("Unknown insight [{$insight}].");
        }

        return view('pages.insight', [
            'insight' => $data,
            'related' => $library->all()->except($insight),
        ]);
    }
}
