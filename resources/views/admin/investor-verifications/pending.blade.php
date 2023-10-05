@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Investor Verifications</a></li>
        <li class="breadcrumb-item active">Pending</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Verification Requests
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Investor</th>
                                        <th>Reference</th>
                                        <th>Tax Number</th>
                                        <th>Managed</th>
                                        <th>Status</th>
                                        <th>ID</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($requests))
                                    @foreach($requests as $req)
                                    <tr>
                                       <td>
                                            <div><a href="{{route('admin.investors.view', ['investor' => $req->investor->reference])}}">{{$req->investor->name}}</a></div>
                                        </td>
                                        <td>
                                           {{$req->reference}}
                                        </td>
                                        
                                        <td>
                                            {{$req->tax_number}}
                                        </td>
                                        
                                        <td>
                                            {{$req->managed_account ? 'Yes' : 'No'}}
                                        </td>
                                        
                                        <td>
                                            @if ($req->status == 2)
                                            Pending
                                            @elseif($req->status == 1)
                                            Approved
                                            @else
                                            Declined
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <a target="_blank" href="{{$req->licence}}" class="btn btn-sm btn-success">View ID</a>
                                        </td>
                                        
                                        <td style="display:flex">
                                            
                                            <a href="javascript:;"
                                                onclick="openApprovalModal({{$req->id}});"
                                                class="btn btn-sm btn-success"> Approve</a>
                                             
                                            <a href="{{route('admin.investor-verifications.decline', ['investorVerificationRequest' => $req->reference])}}"
                                                onclick="return confirm('Are you sure you want to decline this request?');"
                                                class="btn btn-sm btn-danger"> Decline</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="9" class="text-right">
                                            {{$requests->links()}}
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            There are no pending requests
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--/.col-->
                
                <!-- Modal -->
                <div id="applicationApproval" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-sm">
                
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Approve Request</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{route('admin.investor-verifications.approve')}}">
                                {{csrf_field()}}
                                <div class="form-group">
                                    <label>Set Commission Rate</label>
                                    <input type="number" class="form-control" name="commission_rate" required/>
                                </div>
                                <input type="hidden" id="application_id" name="application_id" required/>
                                <hr/>
                                <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                            </form>
                        </div>
                    </div>
                
                  </div>
                </div>
            </div>
            <!--/.row-->
        </div>

    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')
<script>
    function openApprovalModal(id) {
        $("#application_id").val(id);
        $("#applicationApproval").modal();
    }
</script>
@endsection