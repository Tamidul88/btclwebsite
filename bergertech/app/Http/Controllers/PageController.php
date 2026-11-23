<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home', [
            'solutions' => config('solutions'),
        ]);
    }

    public function about()
    {
        return view('pages.about');
    }

    public function resources()
    {
        return view('pages.resources', [
            'solutions' => config('solutions'),
        ]);
    }
}
