<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class SeasonController extends Controller
{

    public function index(Request $request)
    {

    }


    public function show(Request $request)
    {
       return $request->season_num;
    }

}
