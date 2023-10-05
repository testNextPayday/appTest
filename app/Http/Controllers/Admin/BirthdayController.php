<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\BirthdayService;
use App\Http\Controllers\Controller;

class BirthdayController extends Controller
{
    //
    
    /**
     * Provides the logic needed by this controller
     *
     * @var App\Services\BirthdayService
     */
    protected $service;
    
    /**
     * Initialize our birthday service
     *
     * @param   $service
     * @return void
     */
    public function __construct(BirthdayService $service)
    {
        $this->service  = $service;
    }

        
    /**
     * Get the landing page for users
     *
     * @return void
     */
    public function index()
    {
        return view('admin.birthdays.index');
    }


    /**
     * Gets birthdays for today
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function birthdaysToday(Request $request)
    {
        $birthdays = $this->service->birthdaysToday();
        return response()->json($birthdays);
    }
    
    /**
     * Gets the birthday for the next day
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function birthdaysTomorrow(Request $request)
    {
        $birthdays = $this->service->birthdaysTomorrow();
        return response()->json($birthdays);
    }

    
    /**
     * Display upcoming birthdays for the month
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function birthdaySearch(Request $request)
    {
        
        $day = (int)$request->day;
        $month = (int)$request->month;
        $type = $request->type;
        
        $birthdays = $this->service->searchBirthday($day, $month, $type);

        return response()->json($birthdays);
    }
}
