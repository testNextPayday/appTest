@extends('layouts.affiliates')


@section('content')
<div class="content-wrapper">
  <div class="row mb-4">
    <div class="col-12 d-flex align-items-center justify-content-between">
      <h4 class="page-title">Loan {{$loan->reference}}</h4>
      <div>

        @if($loan->is_penalized)
                    
            <span class="badge badge-danger">
                <i class="fa fa-exclamation-triangle"></i>
                Penalised
            </span>
        @endif
        @component('components.loan-status', ['loan' => $loan])
        @endcomponent

        @component('components.loan-statement-button',['loan'=>$loan])
        @endcomponent

        <div id="topUp" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Top up loan</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{route('affiliates.loan-requests.store') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" value="{{$loan->reference}}" name="loan_referenced" />
                            <input type="hidden" value="true" name="is_top_up">
                            @php($users = collect([$loan?->user?->load('employments.employer')]))
                            <max-request-amount :url="'{{route('affiliates.loan-requests.checkmax')}}'" :emi-url="'{{route('affiliates.loan-requests.checkemi')}}'" :users="{{$users}}" :user="{{$loan->user}}"></max-request-amount>

                            <!-- <div class="checkbox mb-2 mt-2">
                                <label for="checkbox1">
                                    <input type="checkbox" id="checkbox1" name="will_collect_incomplete">&nbsp;&nbsp;
                                    Will this loan be taken if its incomplete by the expected withdrawal date?
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="bank_statement" style="display:block;">Bank Statement (3 months from today, JPG or PDF)
                                    <span class="pull-right">Max Size: 1MB</span>
                                </label>
                                <input type="text" value="{{$loan->reference}}" style="display: none;" name="loan_referenced" />
                                <input type="hidden" value="true" name="is_top_up">
                                <input type="file" class="form-control" name="bank_statement" id="bank_statement" required>
                            </div>

                            <div class="form-group">
                                <label for="pay_slip" style="display:block;">Pay Slip (Not more than 3months old)
                                    <span class="pull-right">Max Size: 1MB</span>
                                </label>
                                <input type="file" class="form-control" name="pay_slip" id="pay_slip" required>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="textarea-input">Purpose of Loan</label>
                                    <textarea id="textarea-input" name="comment" rows="3" class="form-control" placeholder="This is your opportunity to increase your chances of getting a Loan">{{old('comment')}}</textarea>
                                </div>
                            </div>

                            <br />

                            <div class="checkbox">
                                <p>
                                    <strong>NOTE:</strong> Our loans are covered by Life insurance cover and Loss of job insurance cover.
                                    Total is 2.5% of requested amount. This amount will be removed from the total amount realized form this request.
                                </p>
                                <label for="checkbox2">
                                    <input type="checkbox" id="checkbox2" name="accept_insurance" required> Accept Insurance Terms
                                </label>
                            </div>
                            <br />

                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-dot-circle-o"></i> Submit</button> -->
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        
      </div>
    </div>
  </div>

  @include('layouts.shared.error-display')

  @component('components.loan-statistics', ['loan' => $loan])
  @endcomponent

  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title bold">CUSTOMER DATA</h4>
          <p>Information about the borrower</p>
          @php($user = $loan?->user)
          <div class="">
            <p>Name: <strong>{{ $user?->name }}</strong></p>
            <p>ID: <strong>{{ $user?->reference }}</strong></p>
            <p>
              <a class="badge badge-primary" href="{{route('affiliates.borrowers.show', $user->reference)}}">More Details...</a>
            </p>
          </div>
          <br />

          <h4 class="card-title bold">LOAN DOCUMENTS</h4>
          <p>User uploaded documents will show up here</p>
          <div>
            @if ($loan->collection_documents)
            @php($documents = json_decode($loan->collection_documents))


            @foreach($documents as $name => $document)
            <a href="{{ asset(Storage::url($document))}}" target="_blank" class="btn btn-primary btn-xs">
              {{ ucwords(str_replace("_", " ", $name)) }}
            </a>
            @endforeach
            @else
            <p class="badge badge-dark">No documents available</p>
            @endif

            @if($loan->status == 2)
                <a class="btn btn-primary btn-sm" href="{{route('view.loan.fulfillment-doc',['reference'=>$loan?->reference])}}" target="_blank">Loan Fulfillment Doc</a>
            @endif
          </div>
          <br />
          <h4 class="card-title bold">AFFILIATE ACTIONS</h4>
          <p>Actions you can perform on the loan at each stage shows up here</p>
          <br />
          <div>
            @component('components.loan-actions-affiliate', ['loan' => $loan])
            @endcomponent
          </div>

        </div>
        <br />
      </div>
    </div>
  </div>
  <br />

    @if($loan->is_penalized)
        @component('components.penalty_schedule_others', ['loan'=> $loan])
        @endcomponent
    @endif
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Loan Repayments</h4>
            <div class="row">
                @if($loan->repaymentPlans->isNotEmpty() && $loan->repaymentPlans->first()->is_new )
                <div class="col-12 table-responsive">
                    <caption>EMI: {{number_format($loan->repaymentPlans->last()->emi,2)}}</caption>
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
        </div>
    </div>


</div>
@endsection

@section('page-js')
@endsection