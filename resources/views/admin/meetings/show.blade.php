@extends('layouts.admin-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                {{ $meeting->when->format('Y-m-d h:i A')}} Meeting Details
            </h4>
            <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#meetingModal">
                <i class="fa fa-pencil"></i> Update Meeting
            </button>
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    <div class="row">
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Venue</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h4 class="mb-0">{{ $meeting->venue }}</h4>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                  <!--<i class="mdi mdi-clock text-muted"></i>-->
                                  <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                </div>
                            </div>
                            <small class="text-gray">
                                &nbsp;
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-success px-4 py-2 rounded">
                                <i class="icon-location-pin text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">State</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h4 class="mb-0">{{ $meeting->state }}</h4>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                </div>
                            </div>
                            <small class="text-gray">
                                &nbsp;
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-info px-4 py-2 rounded">
                                <i class="icon-directions text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Invitees</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">0</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                </div>
                            </div>
                            <small class="text-gray">
                                Meeting Invitees
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-primary px-4 py-2 rounded">
                                <i class="icon-people text-white icon-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <br/>
    
    <div class="row justify-content-center">
        <div class="card col-sm-12">
            <div class="card-body">
                <h4 class="card-title uppercase">Invitees</h4>
                <br/>
                <div class="table-responsive">
                    <table id="order-listing" class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="text-center">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($meeting->invitees as $user)
                                <tr>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->email}}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.affiliates.show', ['affiliate' => $user->reference]) }}"
                                            class="btn btn-xs btn-info">
                                            View Affiliate
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No Invitees</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
     <div class="modal fade" id="meetingModal" tabindex="-1" role="dialog" aria-labelledby="meetingModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Update Meeting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="meetingForm"
                        action="{{ route('admin.meetings.update', ['meeting' => $meeting->id]) }}">
                        @csrf
                        <div class="form-group">
                            <label>When</label>
                            <input type="datetime-local" name="when" required
                                class="form-control{{ $errors->has('when') ? ' is-invalid' : '' }}"
                                value="{{ old('when') ?? $meeting->when->format('Y-m-d\TH:i') }}"/>
                        </div>
                        
                        <div class="form-group">
                            <label>Venue</label>
                            <input type="text" name="venue" required placeholder="Enter meeting venue"
                                class="form-control{{ $errors->has('venue') ? ' is-invalid' : '' }}"
                                value="{{ old('venue') ?? $meeting->venue }}"/>
                        </div>
                        
                        <div class="form-group">
                            <label>State</label>
                            <select type="text" name="state" style="height: 42px;"
                                class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}"
                                value="{{ old('state') }}" required>
                                @foreach(config('unicredit.states') as $state)
                                    <option value="{{ $state }}" {{ ($state === $meeting->state) ? 'selected': ''}}>
                                      {{ $state }}
                                    </option>
                                @endforeach
                            </select>    
                        </div>
                        <div class="form-group">
                            <label>Requirements</label>
                            <textarea placeholder="Specify requirements" name="requirements"
                                class="form-control{{ $errors->has('requirements') ? ' is-invalid' : '' }}">{{ old('requirements') ?? $meeting->requirements }} 
                            </textarea>
                        </div>
                        <div class="form-group">
                            <label>Extra Information</label>
                            <textarea placeholder="Specify extra information" name="extras"
                                class="form-control{{ $errors->has('extras') ? ' is-invalid' : '' }}">{{ old('extras') ?? $meeting->extras }}</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="meetingForm" class="btn btn-success">Update Schedule</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection