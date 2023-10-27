@extends('layouts.staff-new')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Staff refund logs </h4>
                <span id="message"></span>
                <div class="table-responsive mt-3">
                    <table id="order-listing" class="table table-striped table-borderless dt-responsive nowrap default-table m-0 p-0">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Loan</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Date Created</th>
                            </tr>
                        </thead>
                      
                        <tbody>
                        @forelse($refunds as $index => $refund)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td><a target="_blank" href="{{route('staff.accounts.view', [$refund->getUserInfo->reference])}}">{{$refund->getUSerInfo->name}}</a></td>
                                <td>{{$refund->loanInfo->reference}}</td>
                                <td>₦ {{number_format($refund->amount)}}</td>
                                <td>
                                	@if($refund->status == 1)
                                		<span class="badge badge-success">Approved</span>
                                	@elseif($refund->status == 2)
                                		<span class="badge badge-danger">Rejected</span>
                                	@elseif($refund->status == 0)
                                		<span class="badge badge-secondary">Pending</span>
                                	@else

                                	@endif
                                </td>
                                <td>{{$refund->reason}}</td>
                                <td>{{$refund->updated_at}}</td>
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


@endsection
@section('page-js')
@endsection