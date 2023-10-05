<?php

namespace App\Http\Controllers\Investors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FundPaydayNoteController extends Controller
{
    public function monoStatus(){
        return view('investors.promissory-notes.verify-monostatus');
    }
}
