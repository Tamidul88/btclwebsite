<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Laravel 12 ships a bare base controller. AuthorizesRequests is pulled in
 * here so $this->authorize() is available to every controller — without it the
 * call is a fatal error at runtime rather than a denied request, which is the
 * worst possible failure mode for an authorisation check.
 */
abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;
}
