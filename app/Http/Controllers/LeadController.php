<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadRequest;
use App\Models\Lead;

class LeadController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function store(LeadRequest $request)
    {
        Lead::create($request->validated());

        return back()->with('success', 'Thank you for reaching out! We\'ll get back to you shortly.');
    }
}
