<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Bed;
use Illuminate\Http\Request;

class WardManagementController extends Controller
{
    /**
     * Show the Ward & Bed Management page (combines Ward Overview + Patient Admission)
     */
    public function index()
    {
        // Get all wards with bed counts
        $wards = Ward::withCount([
            'beds as total_beds',
            'beds as occupied_beds' => function ($query) {
                $query->where('is_available', false);
            },
            'beds as available_beds' => function ($query) {
                $query->where('is_available', true)
                      ->where(function($q) {
                          $q->whereNull('maintenance_status')
                            ->orWhere('maintenance_status', '!=', 'under_maintenance');
                      });
            }
        ])->get();

        // Get available beds for admission form
        $availableBeds = Bed::where('is_available', true)
            ->where(function($q) {
                $q->whereNull('maintenance_status')
                  ->orWhere('maintenance_status', '!=', 'under_maintenance');
            })
            ->with('ward')
            ->get();

        return view('wards.management', [
            'wards' => $wards,
            'availableBeds' => $availableBeds
        ]);
    }
}