<div class="row">
    @if($loan->repaymentPlans->isNotEmpty() && $loan->repaymentPlans->first()->is_new )
    <div class="col-12 table-responsive">
        <caption>EMI: {{number_format($loan->repaymentPlans->first()->emi,2)}}</caption>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>S/N</th>

                    <th>Total Amount</th>
                    <th>
                        Collected Date
                    </th>
                    <th>Amount Paid</th>
                    <th>Due Date</th>
                    <th>Payment Proof</th>
                    <th>Payment Method</th>
                    <th>Status</th>

                </tr>
            </thead>
            <tbody>
                @php($i = 1)
                @foreach ($loan->repaymentPlans as $plan)
                <tr>
                    <td>{{$i}}</td>

                    <td>{{number_format($plan->totalAmount,2)}}</td>



                    <td>{{$plan->date_paid}}</td>
                    <td>@if($plan->status == 1)
                        {{number_format($plan->paid_amount,2)}}
                        @endif
                    </td>
                    <td>{{$plan->payday}}</td>
                    @if($plan->payment_proof)
                    <td><a target="_blank" href="{{Storage::url($plan->payment_proof)}}">View</a></td>
                    @else
                    <td><a target="_blank" href="{{$plan->payment_proof}}">No proof</a></td>
                    @endif
                    <td>{{$plan->collection_mode}}</td>
                    <td>
                        @if($plan->status)
                        <a class="btn btn-xs btn-block btn-success" href="#">Paid</a>
                        @else
                        <a class="btn btn-xs btn-block btn-warning" href="#">Not Paid</a>
                        @endif
                    </td>




                </tr>
                @php(++$i)
                @endforeach
            </tbody>
        </table>
    </div>
    @else

    <div class="col-12 table-responsive">
        <table id="" class="table">
            <thead>
                <tr>
                    <th>#</th>

                    <th>Total Amount</th>
                    <th>Due Date</th>
                    <th>Balance</th>
                    <th>
                        Collected Date
                    </th>
                    <th>Payment Proof</th>
                    <th>Payment Method</th>
                    <th>Status</th>


                </tr>
            </thead>
            <tbody>
                @forelse($loan->repaymentPlans as $plan)
                <tr>
                    <td>{{$loop->iteration}}</td>

                    <td>₦{{ number_format($plan->totalAmount, 2)}}</td>
                    <td>{{$plan->payday}}</td>

                    <td>
                        @if($loop->iteration === $loan->loanRequest->duration)
                        ₦0.00
                        @else
                        ₦{{ number_format($plan->balance, 2) }}
                        @endif
                    </td>
                    <td>{{$plan->date_paid}}</td>
                    @if($plan->payment_proof)
                    <td><a target="_blank" href="{{Storage::url($plan->payment_proof)}}">View</a></td>
                    @else
                    <td><a target="_blank" href="{{$plan->payment_proof}}">No proof</a></td>
                    @endif
                    <td>{{$plan->collection_mode}}</td>
                    <td>
                        @if($plan->status)
                        <a class="btn btn-xs btn-block btn-success" href="#">Paid</a>
                        @else
                        <a class="btn btn-xs btn-block btn-warning" href="#">Not Paid</a>
                        @endif
                    </td>


                </tr>

                @empty
                <tr>
                    <td colspan="8" class="text-center">Repayments not found</td>
                </tr>
                @endforelse
            </tbody>
        </table>


    </div>
    @endif
</div>