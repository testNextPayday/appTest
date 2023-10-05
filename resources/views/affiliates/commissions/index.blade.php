@extends('layouts.affiliates')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">All Commissions Recieved</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Reference #</th>
                                    <th>Borrower</th>
                                 
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Description</th>                             
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commissions as $commission)
                                    
                                    <tr>
                                        <td>{{ $commission->reference}}</td>
                                        <td>
                                            @if(!is_null($commission->entity))
                                                {{optional($commission->entity->user)->name}}
                                            @else
                                                {{'#######'}}
                                            @endif
                                        </td>
                                       
                                        <td>{{$commission->created_at}}</td>
                                        <td>₦{{ number_format($commission->amount, 2)}}</td>
                                        <td>
                                            {{$commission->description}})
                                        </td>
                                       
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No collected commisisons</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')


    <script src="{{asset('assets/js/data-table.js')}}">
    </script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
   
   
   
        
     
    
@endsection