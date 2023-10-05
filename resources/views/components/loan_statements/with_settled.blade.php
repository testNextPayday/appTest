<div>
  <h3 style="text-align: center;text-transform: uppercase">Loan terms</h3>
  <!-- div for loan terms -->
  <div>
    <table style="width:100%;border:1px solid black" id="loan_terms">
      <tbody>
        <tr>
          <td>
            <div>
              <span style="float:right">{{$loan->reference}}</span>
              <span><b>Loan# </b></span>
            </div>
          </td>
          <td>

            <div>
              <div style="float:right;display:block;margin-right:-80px;">{{number_format($loan->interest,2)}}</div>
              <div><b>Interest Amount</b></div>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div>
              <span style="float:right">{{$loan->created_at->toDateString()}}</span>
              <span><b>Released Date</b></span>
            </div>
          </td>
          <td>
            <div>
              <span style="float:right;display:block;margin-right:-50px;">{{number_format($loan->insurance,2)}}</span>
              <span><b>Insurance</b></span>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div>
              <span style="float:right">{{$loan->due_date->toDateString()}}</span>
              <span><b>Maturity Date</b></span>
            </div>
          </td>
          <td>
            <div>
            <span style="float:right;display:block;margin-right:-50px;">{{number_format($loan->accruedPenalty, 2)}}</span>
              <span><b>Penalty Amount</b></span>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div>
              <span style="float:right">Monthly</span>
              <span><b>Repayment Cycle</b></span>
            </div>
          </td>
          <td>
            <div>
              <span style="float:right;display:block;margin-right:-50px;">{{number_format($loan->total_amount,2)}}</span>
              <span><b>Total Amount Due</b></span>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div>
              <span style="float:right">{{number_format($loan->amount,2)}}</span>
              <span><b>Principal Amount</b></span>
            </div>
          </td>
          <td>
            <div>
              <span style="float:right;display:block;margin-right:-50px;">{{number_format($loan->paid_amount,2)}}</span>
              <span><b>Paid Amount</b></span>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div>
              <span style="float:right">{{number_format($loan->balance,2)}}</span>
              <span><b>Balance Amount</b></span>
            </div>
          </td>
          <td>
              <span style="float:right;color:green">
                Settled
              </span>
              <span>Status</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>



  <!-- div for transaction statement for wallet -->
  <div>
    <h3 style="text-transform: uppercase;text-align:center">Statement of transactions</h3>
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Description</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Balance</th>
        </tr>
      </thead>
      <tbody>


        @if(count($loan->repaymentPlans) > 0)
        <!-- insurance starts here  -->

        <tr>
          <td>{{$loan->created_at->toDateString()}}</td>
          <td>Insurance Deducted</td>
          <td>{{number_format($loan->insurance,2)}}</td>
          <td></td>
          <td>-{{number_format($loan->insurance,2)}}</td>
        </tr>
        <tr>
          <td>{{$loan->created_at->toDateString()}}</td>
          <td>Insurance Paid</td>
          <td></td>
          <td>{{number_format($loan->insurance,2)}}</td>
          <td>{{number_format(0,2)}}</td>
        </tr>
        <!-- insurance ends here -->
        @foreach($loan->repaymentPlans as $plan)
        @if($plan->status == 1 && $plan->paid_amount < 1) @break @endif <tr>
          <td>{{$plan->payday}}</td>
          <td>Loan Installment</td>
          <td>{{number_format($plan->total_amount,2)}}</td>
          <td></td>
          <td></td>
          </tr>
          @if($plan->status == 1)
          <tr>
            <td>{{$plan->payday}}</td>
            <td>Repayment - {{$plan->collection_mode}}</td>
            <td></td>
            <td>{{number_format($plan->paid_amount,2)}}</td>
            <td>{{number_format($plan->wallet_balance,2)}}</td>
          </tr>
          @endif
          @if($plan->transactions->count() > 0)
          @foreach($plan->transactions as $tranx)
          @php
          $wallet_balance = $tranx->wallet_balance > 0 ? - $tranx->wallet_balance : abs($tranx->wallet_balance);

          @endphp
          <tr>
            <td>{{$tranx->created_at->toDateString()}}</td>
            <td class="tranx">{{$tranx->name}}</td>
            <td>{{$tranx->type == '1' ? number_format($tranx->amount,2) : ''}}</td>
            <td>{{$tranx->type == '2' ? number_format($tranx->amount,2) : ''}}</td>
            <td>{{number_format($wallet_balance,2)}}</td>
          </tr>
          @endforeach
          @endif
          @endforeach
          <tr style="font-weight:bold;">
            <td colspan="2">Total</td>
            <td>{{number_format($loan->totalAmountLessPenalty,2)}}</td>
            <td>{{number_format($loan->paid_amount,2)}}</td>
            <span>{{number_format($loan->balanceLessPenalty,2)}}</span>
          </tr>


          @endif
      </tbody>

    </table>

    <div>
      <h4> Settlement Details</h4>
      <table>
        <tbody>
          <tr>
            <td>Amount Paid</td>
            <td>{{number_format($loan->settlement->amount,2)}}</td>
          </tr>
          <tr>
            <td>Collection Method</td>
            <td>{{$loan->settlement->collection_method}}</td>
          </tr>
          <tr>
            <td>Date Paid</td>
            <td>{{$loan->settlement->paid_at}}</td>
          </tr>
        </tbody>
      </table>

    </div>
  </div>


 


</div>