<?php

namespace App\Http\Controllers\Conductor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MapaConductor extends Controller
{
    Public function index(){
        return view('Conductor.index');
    }
}
