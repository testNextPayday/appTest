@extends('layouts.admin-new')

@section('page-css')
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Scheduled Meetings</h4>
            <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#meetingModal">
                <i class="fa fa-plus"></i> Shedule New Meeting
            </button>
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    <div class="row">
        @if(count($meetings))
        @foreach($meetings as $meeting)
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card text-center">
                <div class="card-body">
                    <p>{{ $meeting->when }}</p>
                    <h4>
                        {{ $meeting->venue }}
                    </h4>
                    <p class="text-muted">
                        {{ $meeting->state }}
                    </p>
                    <p class="mt-4 card-text">
                        {{ $meeting->requirements}}
                    </p>
                    <p class="mt-1 card-text">
                        {{ $meeting->extras}}
                    </p>
                    
                    
                    <div class="border-top pt-3">
                        <div class="row">
                            <div class="col-6">
                                <a href="{{ route('admin.meetings.show', ['meeting' => $meeting->id ]) }}"
                                    class="btn btn-sm btn-info btn-block">
                                    <i class="fa fa-angle-double-right"></i> Details
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.meetings.delete', ['meeting' => $meeting->id ]) }}"
                                    class="btn btn-sm btn-danger btn-block"
                                    onclick="return confirm('Are you sure?')">
                                    <i class="fa fa-trash-o"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        <div class="col-12 grid-margin">
            {{$meetings->links('layouts.pagination.default')}} 
        </div>
        @else
            <div class="col-12 grid-margin alert alert-primary text-center">
                No Meetings Scheduled
            </div>
        @endif
    </div>
    
    <div class="modal fade" id="meetingModal" tabindex="-1" role="dialog" aria-labelledby="meetingModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Schedule a new Meeting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="meetingForm"
                        action="{{ route('admin.meetings.index') }}">
                        @csrf
                        <div class="form-group">
                            <label>When</label>
                            <input type="datetime-local" name="when" required
                                class="form-control{{ $errors->has('when') ? ' is-invalid' : '' }}"
                                value="{{ old('when') }}"/>
                        </div>
                        
                        <div class="form-group">
                            <label>Venue</label>
                            <input type="text" name="venue" required placeholder="Enter meeting venue"
                                class="form-control{{ $errors->has('venue') ? ' is-invalid' : '' }}"
                                value="{{ old('venue') }}"/>
                        </div>
                        
                        <div class="form-group">
                            <label>State</label>
                            <select type="text" name="state" style="height: 42px;"
                                class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}"
                                value="{{ old('state') }}" required>
                                @foreach(config('unicredit.states') as $state)
                                  <option value="{{ $state }}">{{ $state }}</option>
                                @endforeach
                            </select>    
                        </div>
                        <div class="form-group">
                            <label>Requirements</label>
                            <textarea placeholder="Specify requirements" name="requirements"
                                class="form-control{{ $errors->has('requirements') ? ' is-invalid' : '' }}">
                                {{ old('requirements') }}
                            </textarea>
                        </div>
                        <div class="form-group">
                            <label>Extra Information</label>
                            <textarea placeholder="Specify extra information" name="extras"
                                class="form-control{{ $errors->has('extras') ? ' is-invalid' : '' }}">
                                {{ old('extras') }}
                            </textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="meetingForm" class="btn btn-success">Schedule</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
@endsection
