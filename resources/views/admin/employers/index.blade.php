@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Employers</h4>
            <h4 class="card-title mb-0">
                <a href="{{ route('admin.employers.manage') }}" class="btn btn-sm btn-primary">
                    <i class="icon-plus"></i> New Employer
                </a>
            </h4>
        </div>
    </div>
    
    @if ($errors->any())
        <div class="row justify-content-center">
            <div class="col-sm-6">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
    
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
                                        <th>Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employers as $employer)
                                        <tr>
                                            <td>
                                                {{ $employer->name}}
                                                @component('components.employer-status', ['status' => $employer->is_verified])
                                                @endcomponent
                                            </td>
                                            <td>{{ $employer->email}}</td>
                                            <td>{{ $employer->phone }}</td>
                                            <td>{{ $employer->address }}</td>
                                            <td>
                                                <a class="badge badge-success"
                                                    href="{{route('admin.employers.view', ['employer' => $employer->id ])}}">
                                                    View Employer
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Data Unavailable</td>
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

<div class="modal fade" id="employer_interest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Set Interest rate</h4><br>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
              
            <form method="POST" id="verifyEmployerForm" action="{{ route('admin.employers.markVerified') }}" accept-charset="UTF-8">
                <div class="modal-body">
                    <small class="text-danger">You have chosen to approve this employer!</small>
                    <input type="number" name="percentage" id="percentage" class="form-control" placeholder="Enter an interest rate" required>
                    <input type="hidden" name="employer_id" id="employer_id" value="0"> 
                    {{ csrf_field() }}
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" onclick="verifyEmployer()" class="btn btn-primary">Save</button>
            </div>
              
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