@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Repayment Loans</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <div class="col-12 table-responsive">
                    <table id="order-listing" class="table">
                        <thead>
                            <tr>
                                <th>Borrower</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Collected date</th>
                                <th>Collector</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            @foreach($repayments as $repayment)
                               <td>{{$repayment->user->name}}</td>
                               <td>{{$repayment->amount}}</td>
                               <td>{{$repayment->payment_method}}</td>
                               <td>{{$repayment->collection_date}}</td>
                               <td>{{$repayment->collector}}</td>
                               <td>{{$repayment->description}}</td>
                               
                               <td>{{$repayment->is_verified == 1 ? 'confirmed' : 'unconfirmed'}}</td>
                               <td><button onclick="document.getElementById('confirm-{{$repayment->id}}').submit()" class="btn btn-xs btn-primary">Confirm</button><button class="btn btn-xs btn-danger" onclick="document.getElementById('delete-{{$repayment->id}}').submit()">Delete</button></td>
                              <form id="delete-{{$repayment->id}}" action="{{route('staff.repayment.delete',$repayment)}}" method="post">
                                  {{csrf_field()}}
                                  {{method_field('DELETE')}}
                              </form>
                              <form id="confirm-{{$repayment->id}}" method="post" action="{{route('admin.repayment.confirm',$repayment)}}">
                                 {{ csrf_field() }}
                                
                              </form>
                           @endforeach
                            </tr>
                      
                               
                        </tbody>
                    </table>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection