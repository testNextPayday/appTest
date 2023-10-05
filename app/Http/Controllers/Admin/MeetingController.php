<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Meeting;
use Carbon\Carbon;
use Notification;
use App\Notifications\Affiliates\MeetingScheduleUpdated;

class MeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::latest()->paginate(30);
        return view('admin.meetings.index', compact('meetings'));
    }
    
    /**
     * Schedules a new meeting
     */
    public function store(Request $request)
    {
        $rules = $this->getRules();
        
        $this->validate($request, $rules);
        
        $data = $request->only(array_keys($rules));
        $data['when'] = Carbon::parse($request->when)
                                ->toDateTimeString();
        
        if (Meeting::create($data)) {
            return redirect()->back()->with('success', 'Meeting scheduled successfully');
        }
        
        return redirect()->back()->with('failure', 'Meeting scheduling failed. Try again later');
    }
    
    /**
     * Updates an existing meeting
     */
    public function update(Request $request, Meeting $meeting)
    {
        $rules = $this->getRules();
        
        $data = $request->only(array_keys($rules));
        
        $newDate = Carbon::parse($request->when);
        
        $oldVenue = $meeting->venue;
        $oldDate = $meeting->when;
        
        $data['when'] = $newDate->toDateTimeString();
                                
        if ($meeting->update($data)) {
            
            // Check if an update mail should be sent
            
            if ($meeting->invitees()->count() && (!$newDate->equalTo($oldDate) || $data['venue'] !== $oldVenue)) {
                // Critical Info changed, Send Notification
                Notification::send($meeting->invitees, new MeetingScheduleUpdated($meeting->fresh()));
            }
            
            return redirect()->back()->with('success', 'Meeting updated successfully');
        }
        
        return redirect()->back()->with('failure', 'Meeting update failed. Try again later');
    }
    
    /**
     * Shows information about a single meeting
     */
    public function show(Meeting $meeting)
    {
        return view('admin.meetings.show', compact('meeting'));    
    }
    
    /**
     * Deletes a meeting
     * 
     */
    public function delete(Meeting $meeting)
    {
        try {
            $meeting->delete();
            
            return redirect()->back()
                            ->with('success', 'Meeting deleted successfully');
            
        } catch(\Exception $e) {
            
            return redirect()->back()
                            ->with('failure', 'Meeting could not be deleted: ' . $e->getMessage() );
        }
    }
    
    private function getRules()
    {
        return [
            'venue' => 'required',
            'state' => 'required',
            'when' => 'required|date',
            'requirements' => 'nullable',
            'extras' => 'nullable'
        ];
    }
}
