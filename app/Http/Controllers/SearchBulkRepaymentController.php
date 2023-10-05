<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchBulkRepaymentController extends Controller
{
    //

    public function searchBorrowers(Request $request)
    {
        $query = $request->query('query');
        $users = User::where('name', 'like', '%' . $query . '%')->limit(10)->get();

        return response()->json($users);
    }
}
