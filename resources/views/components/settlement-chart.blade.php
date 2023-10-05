<div>

    <section style="">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Principal Amount</td>
                    <td>{{number_format($loan->amount,2)}}</td>
                    <td></td>
                    <td>Released Date</td>
                    <td>{{$loan->created_at}}</td>
                </tr>
                <tr>
                    <td> EMI</td>
                    <td>
                        {{$loan->repaymentPlans->first()->is_new ? number_format($loan->emi,2) : number_format($loan->repaymentPlans->first()->emi + $loan->repaymentPlans->first()->management_fee,2)}}

                    </td>
                    <td></td>
                    <td>Payment Closed</td>
                    <td>{{$loan->closedPayments()->count()}}</td>
                </tr>
                <tr>
                    <td>Loan Tenor</td>
                    <td>{{$loan->duration}}</td>
                    <td></td>
                    <td>Penalty</td>
                    <td>
                        @php($cur_month = $loan->created_at->diffInMonths(now()))
                        @if($cur_month <= 3) {{10}}% @elseif($cur_month <=6) {{7.5}}% @elseif($cur_month <=9) {{5}}% @else {{0}}% @endif 
                    </td>
                     </tr> 
                    </tbody> </table> </section> <section>
                            <br>
                            <h5>Loan Fulfilment</h5>

                            <table class="table table-bordered">
                                @php($loan_wallet = $loan->user->loan_wallet)
                                <tbody>
                                    <tr>
                                        <td>Released Amount</td>
                                        <td>{{number_format($loan->amount,2)}}</td>
                                    </tr>
                                    <tr>
                                        <td> Interest and Fees to Date</td>
                                        <td>{{number_format($loan->accrued_amount,2)}}</td>
                                    </tr>
                                    @if($loan_wallet > 0)
                                    <tr>
                                        <td>  Late Repayment Charges</td>
                                        <td>{{number_format($loan_wallet,2)}}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><b> Current Value of Loan</b></td>
                                        <td>{{number_format($loan->current_value,2)}}</td>
                                    </tr>

                                    
                                    <tr>
                                        <td>Less: Repayments</td>
                                        <td>{{number_format($loan->deductions,2)}}</td>
                                    </tr>
                                    <tr>
                                        <td> Repayment Wallet Balance</td>
                                        <td>{{number_format($loan->repayment_wallet,2)}}</td>
                                    </tr>
                                    <!-- <tr>
                                        <td>- Total Deductions</td>
                                        <td>{{number_format($loan->total_deductions,2)}}</td>
                                    </tr>

                                -->
                                     <!-- If the loan wallet is greater than 0 add it up -->
                                   
                                    <tr>
                                        <td><b>  Value on Closure</b></td>
                                        <td>{{number_format($loan->closing_value)}}</td>
                                    </tr>
                                   
                                    <tr>
                                        <td>Add:  {{$loan->is_penalized ? 'Penal Charge' : 'Preliquidation Charge'}}</td>
                                        <td>{{number_format($loan->penal_charge,2)}}</td>
                                    </tr>

                                     <!-- If there is a negative balance in the wallet greater than 100 add to the settlement amount -->
                                     @if($loan_wallet < 0 && abs($loan_wallet) > 100 && !$loan->is_penalized) 
                                        <tr>
                                            <td>  Late Repayment Charges</td>
                                            <td>{{number_format(abs($loan->user->loan_wallet),2)}}</td>
                                        </tr>
                                    @endif

                  
                                    <tr>
                                        
                                        <td><b>  Total Due @ {{now()}}</b></td>
                                        <td>{{ number_format($loan->closing_value + $loan->penal_charge, 2) }}</td>
                                    </tr>
                                    
                                </tbody>
                            </table>
    </section>

</div>