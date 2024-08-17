<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MapLayout extends Component
{
    // public $rolUser;
    // public $positions;

    // public function __construct($rolUser, $positions)
    // {
    //     $this->rolUser = $rolUser;
    //     $this->positions = $positions;
    // }
    
    public function render()
    {
        return view('layouts.map');
    }
}
