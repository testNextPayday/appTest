@extends('layouts.investor')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Wallet Transactions</h4>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card support-pane-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Wallet Transactions</h4>
                    </div>
                    <div class="table-responsive support-pane no-wrap">
                        @if(count($transactions))
                        <div style="display:flex; justify-content:space-between;">
                             <form class="form-inline" method="get" action="{{route('investors.transactions')}}">
                             <label>From</label>
                             <input type="date" class="form-control mb-2 mr-sm-2" value="{{$from}}" name="from"/>
                             <label>To</label>
                             <input type="date" class="form-control mb-2 mr-sm-2" value="{{$to}}" name="to"/>
                             <input type="submit" class="btn btn-primary"  value="Submit"/>
                             </form>
                       
                        </div>
                        
                        
                        <table id="order-listing" class="table">
                            <thead>
                                <tr style="background:lightgray;text-transform:uppercase;font-weight:bold;">
                                <th>Reference</th>
                                <th>Description</th>
                                <th>Direction</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            
                            @foreach($transactions as $transaction)
                               <tr>
                                   <td>{{$transaction->reference}}</td>
                                   <td>{{$transaction->description}}</td>
                                   <td>{{$transaction->direction == 1 ? 'INFLOW' : 'OUTFLOW'}}</td>
                                   <td>₦ {{ number_format($transaction->amount, 2) }}</td>
                                   <td>{{ $transaction->created_at->format('l jS \\of F Y') }}</td>
                               </tr>
                             @endforeach
                        </table>
                       
                        @else
                        <div class="t-row text-center">
                        <form class="form-inline" method="get" action="{{route('investors.transactions')}}">
                             <label>From</label>
                             <input type="date" class="form-control mb-2 mr-sm-2" value="{{$from}}" name="from"/>
                             <label>To</label>
                             <input type="date" class="form-control mb-2 mr-sm-2" value="{{$to}}" name="to"/>
                             <input type="submit" class="btn btn-primary" value="Submit"/>
                            
               
                         </form>
                            <h4>You've made no transactions yet</h4>
                        </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <p class="mb-0 d-none d-md-block"></p>
                        <nav>
                           
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
@endsection
@section('page-js')


    <script src="{{asset('assets/js/data-table.js')}}">
    </script>
   
   
   
        
     
    
@endsection