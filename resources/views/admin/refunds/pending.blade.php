@extends('layouts.admin-new')

@section('content')
<style>
    td {
        word-break : break-all;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Pending refunds </h4>
                <span id="message"></span>
                <div class="table-responsive mt-3">
                    <table id="order-listing" class="table table-responsive">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Loan</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                                <th>Reason</th>
                               
                            </tr>
                        </thead>
                      
                        <tbody>
                        @forelse($refunds as $index => $refund)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td>{{$refund->getUSerInfo->name}}</td>
                                <td><a href="{{route('admin.loans.view', ['reference'=> $refund->loanInfo->reference])}}" target="_blank">{{$refund->loanInfo->reference}}</a></td>
                                <td>₦ {{number_format($refund->amount)}}</td>
                                <td>{{$refund->status = 1 ? 'Pending' : ''}}</td>
                                <td>{{$refund->created_at}}</td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#refund" data-amount="{{$refund->amount}}" data-user_id="{{$refund->loanInfo->user_id}}" data-reason="{{$refund->reason}}" data-loan_id="{{$refund->loanInfo->id}}" data-name="{{$refund->getUSerInfo->name}}" data-reference="{{$refund->loanInfo->reference}}" data-url_data="{{route('admin.update.refund', ['id'=>$refund->id, 'status'=>0])}}">Update</button>

                                    <form style="display:inline" method="POST" action="{{route('admin.update.refund', ['id' => $refund->id, 'status' => 2])}}">
                                		{{ csrf_field() }} {{ method_field('PATCH') }}
                                		<button class="btn btn-danger btn-sm" type="submit">Reject</button>
                                	</form>

                                    <form style="display:inline" method="POST" action="{{route('admin.update.refund', ['id' => $refund->id, 'status' => 1])}}">
                                		{{ csrf_field() }} {{ method_field('PATCH') }}
                                		<button class="btn btn-success btn-sm" type="submit">Approve</button>
                                	</form>

                                </td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#reason{{$refund->id}}" >Reason</button>
                                    <div class="modal fade" id="reason{{$refund->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-sm" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-10"><span>{{$refund->reason}}</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                </td>
                            </tr>
                        @empty
                     	  <td colspan="6"> No record found</td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div><!-- end row -->


<div class="modal fade" id="refund" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update refund</h4><br>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form method="post" action="" id="myForm">
                    {{ csrf_field() }} {{ method_field('PATCH') }}
                <div class="modal-body">
                    <div class="form-group">
                         <label>  <span id="name"></span></label>
                         <input type="hidden" name="user_id" id="user_id" value="" />
                        <input type="hidden" name="loan_id" id="loan_id" value="" >
                        <input type="text" id="reference" value="" readonly class="form-control">
                    </div>
                 
                    <div class="form-group">
                        <label>Amount:</label>
                        <input type="text" name="amount" id="amount" class="form-control" value="" required>
                    </div>
                    <div class="form-group">
                        <label>Refund reason:</label>
                        <textarea type="text" name="reason" id="reason" class="form-control"></textarea>
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

<style>
    div.col-md-10 > #text {

        width:40px;
    }
</style>

@endsection
@section('page-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
<script type="text/javascript">
$('#refund').on('show.bs.modal', function(e) {

   var data = $(e.relatedTarget).attr('data-url_data');
   
   $("#myForm").attr("action", data);

   var user_id = $(e.relatedTarget).attr('data-user_id'); 
   $("#user_id").val(user_id);

   var amount = $(e.relatedTarget).attr('data-amount');
   $("#amount").val(amount);

   var reason = $(e.relatedTarget).attr('data-reason');
   $("#reason").text(reason);

   var loan_id = $(e.relatedTarget).attr('data-loan_id');
   $("#loan_id").val(loan_id);

  var reference = $(e.relatedTarget).attr('data-reference');
   $("#reference").val(reference);

  var name = $(e.relatedTarget).attr('data-name');
   $("#name").text(name);
});
 
</script>
@endsection