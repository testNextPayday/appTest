@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Buckets</h4>
            <h4 class="card-title mb-0"><a href="#" data-toggle="modal" data-target="#newBucket" class="btn btn-sm btn-primary"><i class="icon-close"></i> New Bucket</a></h4>
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
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
                                        <th>Employer Count</th>
                                        <th>Date Created</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($buckets as $bucket)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.buckets.show', ['bucket' => $bucket->slug]) }}">
                                                    {{ $bucket->name}}
                                                </a>
                                            </td>
                                            <td>{{ $bucket->employers_count}} {{ str_plural('Employer', $bucket->employers_count)}}</td>
                                            <td>{{ $bucket->created_at}}</td>
                                            <td>
                                                {{-- <a class="badge badge-success" href="{{route('admin.sweep.bucket', ['bucket' => $bucket->slug])}}"> --}}
                                                   {{-- Sweep --}}
                                                {{-- </a>&nbsp; --}}
                                                <a class="badge badge-danger"
                                                    onclick="return confirm('Are you sure?')"
                                                    href="{{route('admin.buckets.delete', ['bucket' => $bucket->slug])}}">
                                                    Delete
                                                </a>
                                            </td>
                                            
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No Buckets</td>
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
</div>

<div class="modal fade" id="newBucket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Bucket</h4><br>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
              
            <form method="post" action="{{route('admin.buckets.store')}}">
                {{csrf_field()}}
                <div class="modal-body">
                    <div id="new_employer_section">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label class="control-label" for="name"><strong>Bucket Name</strong></label>
                                <input type="text" name="name" id="name"
                                    class="form-control" required 
                                    placeholder="Name of Bucket" value="{{ old('name') }}" title="Name of Bucket">
                            </div>
                        </div>
                            
                        <hr/>
                        <h6>SWEEP PARAMS</h6>
                        <hr/>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="control-label" for="sweep_start_day"><strong>Sweep Start Day</strong></label>
                                <input type="number" name="sweep_start_day" id="sweep_start_day" value="{{old('sweep_start_day')}}"
                                    class="form-control" required >
                            </div>
                                
                            <div class="form-group col-sm-4">
                                <label class="control-label" for="sweep_end_day"><strong>Sweep End Day</strong></label>
                                <input type="number" name="sweep_end_day" id="sweep_end_day" value="{{old('sweep_end_day')}}"
                                    class="form-control" required >
                            </div>
                            
                            <div class="form-group col-sm-4">
                                <label class="control-label" for="sweep_frequency"><strong>Sweep Frequency</strong></label>
                                <input type="number" name="sweep_frequency" id="sweep_frequency" value="{{old('sweep_frequency')}}"
                                    class="form-control" required >
                            </div>
                                
                        </div>
                        
                        <hr/>
                        <h6>PEAK PARAMS</h6>
                        <hr/>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="control-label" for="peak_start_day"><strong>Peak Start Day</strong></label>
                                <input type="number" name="peak_start_day" id="peak_start_day" value="{{old('peak_start_day')}}"
                                    class="form-control" required >
                            </div>
                                
                            <div class="form-group col-sm-4">
                                <label class="control-label" for="peak_end_day"><strong>Peak End Day</strong></label>
                                <input type="number" name="peak_end_day" id="peak_end_day" value="{{old('peak_end_day')}}"
                                    class="form-control" required >
                            </div>
                            
                            <div class="form-group col-sm-4">
                                <label class="control-label" for="peak_frequency"><strong>Peak Frequency</strong></label>
                                <input type="number" name="peak_frequency" id="peak_frequency" value="{{old('peak_frequency')}}"
                                    class="form-control" required >
                            </div>
                                
                        </div>
                                    
                    </div>
                </div>
              
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save</button>
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

<script>
$('#employer_interest').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var id = button.data('id') // Extract info from data-* attributes
  var modal = $(this)
  modal.find('#employer_id').val(id)
})
        
function verifyEmployer(){
    var employer_id = $("#employer_id").val();
    // console.log(employer_id);
    if (employer_id == 0) {
        alert('Please Enter an Amount');
        return false;
        }
    document.getElementById("verifyEmployerForm").submit();
}

</script>

@endsection