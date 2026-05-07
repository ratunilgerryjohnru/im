<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use Illuminate\Http\Request;

class WardController extends Controller
{
    /**
     * Display a listing of the wards.
     */
    public function index()
    {
        // Fetches all wards from Supabase through the Ward Model
        $wards = Ward::all();

        return view('wards.index', compact('wards'));
    }

    /**
     * Display the specific ward and its beds.
     */
    public function show(Ward $ward)
    {
        /**
         * Route Model Binding automatically finds the Ward by ID.
         * For the WELLMEADOWS system, this is where you'd eventually 
         * load the beds relationship: $ward->load('beds');
         */
        return view('wards.show', compact('ward'));
    }

    /**
     * Store a newly created ward in the database.
     */
    public function store(Request $request)
    {
        // 1. Validation: Ensures the data matches your database constraints
        $validated = $request->validate([
            'ward_name' => 'required|string|max:255',
            'category' => 'required|string',
            'total_beds' => 'required|integer|min:1',
            'available_beds' => 'required|integer|min:0|lte:total_beds',
        ]);

        // 2. Creation: Saves the validated data to the 'wards' table
        Ward::create($validated);

        // 3. Redirection
        return redirect()->route('wards.index')->with('success', 'New ward registered successfully!');
    }
}