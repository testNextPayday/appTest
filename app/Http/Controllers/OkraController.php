<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OkraController extends Controller
{
    public function submitRequest(){
        return view('users.okra.create');
    }
}
