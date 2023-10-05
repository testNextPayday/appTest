<div class="card">

    <div class="card-body">
        <h4 class="card-title"> Loan Transactions</h4>
        <div class="row">
            <div class=" col-12 table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Loan Reference</th>
                            <th>Transactions</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Date </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($i = 1)
                        @forelse($loan->transactions as $transaction)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{$transaction->loan->reference}}</td>
                            <td>{{$transaction->name}}</td>
                            <td>{{($transaction->type == 1) ? 'Debit' : 'Credit'}}</td>
                            <td>{{number_format($transaction->amount,2)}}</td>
                            <td>{{$transaction->description}}</td>
                            <td>{{$transaction->created_at}}</td>
                        </tr>
                        @empty
                        <p> No transaction has been entered for this loan</p>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>