<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DocController extends Controller
{
    public function index()
    {
        return Inertia::render('docs/Index');
    }
}
