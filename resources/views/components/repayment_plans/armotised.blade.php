<div class="col-12 table-responsive">
    @if($loan->upfront_interest)
        @if($loan->repaymentPlans->last()->status == 1)
            <caption>CURRENT EMI: NIL </caption>
        @else
            <caption>CURRENT EMI: {{number_format($loan->repaymentPlans->where('status', 0)->first()->emi,2)}}</caption>
        @endif
    @else
        <caption>EMI: {{number_format($loan->repaymentPlans->last()->emi,2)}}</caption>
    @endif
   
    <table class="table table-bordered">

        <thead>
            <tr>
                <th>S/N</th>
                <th>Buffer Status</th>
                <th>Begining Balance</th>
                <th>Payment</th>
                <th>Principal</th>
                <th>Interest</th>
                <th>Ending Balance</th>

                <th>Wallet Balance</th>
                <th>Management Fee</th>
                <th>
                    Collected Date
                </th>
                <th>Amount Paid</th>
                <th>Due Date <br>
                     <a  class=" mt-2 badge badge-sm badge-danger" href="{{ route('admin.loans.back_date_paydays', [$loan->id, 'backdate'] ) }}"><i class="fa fa-edit  " data-toggle="modal"
                    data-target="#backDateSingleDueDate"></i> <small>Back Date</small> </a>
                        <br>
                    <a  class=" mt-2 badge badge-sm badge-warning" href="{{ route('admin.loans.back_date_paydays', [$loan->id, 'postdate'] ) }}"><i class="fa fa-edit  " data-toggle="modal"
                        data-target="#backDateSingleDueDate"></i> <small>Post Date</small> </a>
                </th>
                <th>Payment Proof</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Action</th>
                <th>
                    Delete
                </th>
            </tr>
        </thead>
        <tbody>
            @php($i = 1)
            @foreach ($loan->repaymentPlans as $plan)
            <tr>
                <td>{{$i}}</td>
                <td><buffer-status :buffers="{{$plan->buffers}}"></buffer-status></td>
                <td>{{number_format($plan->begin_balance,2)}}</td>
                <td>{{number_format($plan->payments,2)}}</td>
                <td>{{number_format($plan->principal,2)}}</td>
                <td>{{number_format($plan->interest,2)}}</td>
                <td>{{number_format($plan->end_balance,2)}}</td>

                <td>
                    @if(! is_null($plan->wallet_balance))
                    {{number_format($plan->wallet_balance,2)}}
                    @else

                    @endif
                </td>
                <td>{{number_format($plan->management_fee,2)}}</td>
                <td>{{$plan->date_paid}}</td>
                <td>
                    @if($plan->status == 1)
                    {{number_format($plan->paid_amount,2)}}
                    @endif
                </td>
                <td>{{$plan->payday}}
                    {{-- <i class="fa fa-edit text-primary  " data-toggle="modal"
                    data-target="#backDateSingleDueDate{{ $plan->id }}"></i> --}}
                </td>
                @if($plan->payment_proof)
                <td><a target="_blank" href="{{Storage::url($plan->payment_proof)}}">View</a></td>
                @else
                <td><a target="_blank" href="{{$plan->payment_proof}}">No proof</a></td>
                @endif
                <td>
                    @if($plan->status == 0)
                    @if(!$plan->collection_mode)
                    <select id="select-confirm-method" class="form-control" style="width:auto;" onchange="attachCollectionMethod(event,{{$plan->id}})">
                        <option value="Cash">Cash</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Deposit">Deposit</option>
                        <option value="DDAS">DDAS</option>
                        <option value="Transfer">Transfer</option>
                        <option value="Remita">Remita</option>
                        <option value="Paystack">Paystack</option>
                        <option value="Set-off">Set-off</option>
                        <option value="RavePay">RavePay</option>
                    </select>
                    @else
                    {{$plan->collection_mode}}
                    @endif
                    @else
                    {{$plan->collection_mode}}
                    @endif

                </td>
                <td>
                    @if($plan->status)
                    <a class="btn btn-xs btn-block btn-success" href="#">Paid</a>
                    @else
                    <a class="btn btn-xs btn-block btn-warning" href="#">Not Paid</a>
                    @endif
                </td>
                <td>
                    @if($plan->status == true)
                    <button class="btn btn-warning" onclick="var status = confirm('Are you sure?'); if(status == true){document.getElementById('unconfirm-repayment{{$plan->id}}').submit()}">unconfirm</button>
                    @else
                    <button class="btn btn-primary" onclick="var status = confirm('Are you sure?'); if(status == true){document.getElementById('confirm-repayment{{$plan->id}}').submit()}">Confirm</button>
                    @endif
                </td>
                <td><button onclick="var status = confirm('Are you sure?'); if(status == true){document.getElementById('delete-repayment{{$plan->id}}').submit()}">Delete</button></td>


            </tr>
            @php(++$i)
            @endforeach
        </tbody>
    </table>
</div>






@forelse($loan->repaymentPlans as $plan)
    <div class="modal fade" id="backDateSingleDueDate{{ $plan->id }}" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Change Single Due Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form">
                        <form action="{{ route('admin.loans.change_single_due_date') }}" method="post">@csrf
                            <div class="">
                                <div class="form-group col-md-7">
                                    <label for="date">Change Date</label>
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <input type="date" name="new_date" value="{{ $plan->payday }}"
                                        class="form-control">
                                </div>
                                <div class="form-group col-md-5">
                                    <button class="btn btn-sm btn-success">Update</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>
            </div>
        </div>
    </div>

@empty
    <tr>
        <td colspan="8" class="text-center">Repayments not found</td>
    </tr>
@endforelse
