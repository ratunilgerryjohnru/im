<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Closure; // Added this to prevent type errors

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View|Closure|string
    {
        // This ensures Laravel looks in resources/views/layouts/app.blade.php
        return view('layouts.app');
    }
}