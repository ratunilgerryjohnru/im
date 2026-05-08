<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use Illuminate\Http\Request;

class WardController extends Controller
{
    public function index()
    {
        $wards = Ward::all();
        return view('wards.index', compact('wards'));
    }

    public function show(Ward $ward)
    {
        // Laravel uses 'ward_id' automatically now because of your Model settings
        return view('wards.show', compact('ward'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ward_name'      => 'required|string|max:255',
            'ward_type'      => 'required|string', // Updated to match Supabase
            'location'       => 'nullable|string',  // Added based on your schema
            'total_beds'     => 'required|integer|min:1',
            'available_beds' => 'required|integer|min:0|lte:total_beds',
            'ward_phone'     => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        Ward::create($validated);

        return redirect()->route('wards.index')->with('success', 'Ward registered successfully!');
    }
}