<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountsController extends Controller
{
    public function index($model, $type)
    {
        $options = [
            'investors' => 'App\Models\Investor',
            'borrowers' => 'App\Models\User'
        ];
        
        if ($dataSource = @$options[$model]) {
            $data = $dataSource::all();
            
            return view('admin.accounts.data', compact('model', 'type', 'data'));
        }
        
        return redirect()->back()->with('failure', 'Data not found');
    }
}
