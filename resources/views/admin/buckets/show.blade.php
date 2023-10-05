@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                {{ $bucket->name}}
            </h4>
        </div>
    </div>
    
    
    @include('layouts.shared.error-display')
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4>Bucket Data</h4>
                            <br/>
                            <br/>
                        </div>
                        
                        <div class="col-sm-4">
                            <h6>Normal Sweep Params</h6>
                            <hr/>
                            <p>Sweep Start Day: <strong>Day {{$bucket->sweep_start_day}} Monthly</strong></p>
                            <p>Sweep End Day: <strong>Day {{$bucket->sweep_end_day}} Monthly</strong></p>
                            <p>Sweep Frequency: <strong>{{$bucket->sweep_frequency}} Times Daily</strong></p>
                        </div>
                        <div class="col-sm-4 offset-sm-1">
                            <h6>Peak Sweep Params</h6>
                            <hr/>
                            <p>Peak Start Day: <strong>Day {{$bucket->peak_start_day}} Monthly</strong></p>
                            <p>Peak End Day: <strong>Day {{$bucket->peak_end_day}} Monthly</strong></p>
                            <p>Peak Frequency: <strong>{{$bucket->peak_frequency}} Times Daily</strong></p>
                        </div>
                        <div class="col-sm-12">
                            <a href="#" data-toggle="modal" data-target="#editBucket" class="btn btn-sm btn-warning"><i class="icon-info"></i> Edit Bucket</a>
                            <a href="#" data-toggle="modal" data-target="#addEmployerToBucket" class="btn btn-sm btn-primary"><i class="icon-plus"></i> Add Employers</a>
                            {{-- <a class="btn btn-success" href="{{route('admin.sweep.bucket', ['bucket' => $bucket->slug])}}"> --}}
                            {{--    <i class="icon-energy"></i> --}}
                            {{--    Sweep --}}
                            {{-- </a> --}}
                        </div>
                        
                    </div>
                    <br/>
                </div>
            </div>
        </div>
        <!--/.col-->
    </div>
    
    <br/>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table id="order-listing" class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bucket->employers as $employer)
                                        <tr>
                                            <td>
                                                {{ $employer->name}}
                                                @component('components.employer-status', ['status' => $employer->is_verified])
                                                @endcomponent
                                            </td>
                                            <td>{{ $employer->email}}</td>
                                            <td>{{ $employer->phone }}</td>
                                            <td>
                                                <a class="badge badge-danger"
                                                    onclick="return confirm('Are you sure?')"
                                                    href="{{route('admin.buckets.employer.remove', ['employer' => $employer->id])}}">
                                                    Remove
                                                </a>&nbsp;
                                                <a class="badge badge-success"
                                                    href="{{route('admin.employers.view', ['employer' => encrypt($employer->id)])}}">
                                                    View Employer
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">This bucket has no employers yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br/>
</div>

<div class="modal fade" id="editBucket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Bucket</h4><br>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
              
            <form method="post" action="{{route('admin.buckets.update', ['bucket' => $bucket->slug])}}">
                {{csrf_field()}}
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label class="control-label" for="name"><strong>Bucket Name</strong></label>
                            <input type="text" name="name" id="name"
                                class="form-control" required 
                                placeholder="Name of Bucket" value="{{ old('name') ?? $bucket->name }}" title="Name of Bucket">
                        </div>
                    </div>
                            
                    <hr/>
                    <h6>SWEEP PARAMS</h6>
                    <hr/>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="sweep_start_day"><strong>Sweep Start Day</strong></label>
                            <input type="number" name="sweep_start_day" id="sweep_start_day" value="{{old('sweep_start_day') ?? $bucket->sweep_start_day}}"
                                class="form-control" required >
                        </div>
                            
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="sweep_end_day"><strong>Sweep End Day</strong></label>
                            <input type="number" name="sweep_end_day" id="sweep_end_day" value="{{old('sweep_end_day') ?? $bucket->sweep_end_day}}"
                                class="form-control" required >
                        </div>
                        
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="sweep_frequency"><strong>Sweep Frequency</strong></label>
                            <input type="number" name="sweep_frequency" id="sweep_frequency" value="{{old('sweep_frequency') ?? $bucket->sweep_frequency}}"
                                class="form-control" required >
                        </div>
                            
                    </div>
                        
                    <hr/>
                    <h6>PEAK PARAMS</h6>
                    <hr/>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="peak_start_day"><strong>Peak Start Day</strong></label>
                            <input type="number" name="peak_start_day" id="peak_start_day" value="{{old('peak_start_day') ?? $bucket->peak_start_day}}"
                                class="form-control" required >
                        </div>
                            
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="peak_end_day"><strong>Peak End Day</strong></label>
                            <input type="number" name="peak_end_day" id="peak_end_day" value="{{old('peak_end_day') ?? $bucket->peak_end_day}}"
                                class="form-control" required >
                        </div>
                        
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="peak_frequency"><strong>Peak Frequency</strong></label>
                            <input type="number" name="peak_frequency" id="peak_frequency" value="{{old('peak_frequency') ?? $bucket->peak_frequency}}"
                                class="form-control" required >
                        </div>
                            
                    </div>
                                    
                </div>
              
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
              
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="addEmployerToBucket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Employer to Bucket</h4><br>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
              
            <form method="post" action="{{route('admin.buckets.employers.add', ['bucket' => $bucket->slug])}}">
                {{csrf_field()}}
                <div class="modal-body">
                    @forelse(App\Models\Employer::whereNull('bucket_id')->get() as $employer)
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label class="control-label" for="name">
                                <input type="checkbox" name="employer_{{$employer->id}}" id="name"
                                    value="{{ $employer->id }}">&nbsp;
                                <strong>{{ $employer->name }}</strong>
                            </label>
                        </div>
                    </div>
                    @empty
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <p>There are no free employers</p>
                        </div>
                    </div>
                    @endforelse
                </div>
              
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Add Employers</button>
                </div>
            </form>
              
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@endsection

@section('page-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection