@extends('layouts.staff-new')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Loan Repayments  </h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="repayments-table" class="table">
                            <thead>
                                <tr>
                                    <th style="display:none">
                                        Ref
                                    </th>
                                    <th>Name</th>
                                    <th>Loan</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Payday</th>
                                    <th>
                                        Payment Proof
                                    </th>
                                    <th>Collection date</th>    
                                    <th>Status</th>
                        
                                    <th>Reciept</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingUploads as $upload)
                                    <tr>
                                        <td style="display:none">{{$upload->id}}</td>
                                        <td>{{ $upload->user->name }}</td>
                                        <td>{{optional($upload->loan)->reference }}</td>
                                        <td>{{ $upload->collection_method == null ? 'No collection' : $upload->collection_method  }}</td>
                                        
                                        <td>{{ number_format($upload->amount, 2) }}</td>
                                        <td>{{ $upload->plan ? optional($upload->plan->payday)->toDateString() : 'No plan found'}}</td>
                                        @if($upload->payment_proof)
                                        <td><a target="_blank" href="{{Storage::url($upload->payment_proof)}}">View</a></td>
                                        @else
                                         <td><a target="_blank" href="{{$upload->payment_proof}}">No proof</a></td>
                                         @endif
                                        <td>{{$upload->collection_date}}</td>
                                        <td>
                                            @if($upload->status == 1)
                                                <a class="badge badge-success" href="#">Paid</a>
                                            @else
                                                <a class="badge badge-warning" href="#">Not Paid</a>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($upload->plan)
                                                @if($upload->plan->status)
                                                    <a class="label label-success" href="#">Paid</a>
                                                @else
                                                    @if($upload->plan->payday <= Carbon\Carbon::today() && !$upload->plan->status)
                                                        <a class="btn btn-xs btn-warning" href="{{route('admin.repayments.order_debit', ['plan_id' => encrypt($upload->plan->id)])}}">Issue Debit Order</a>
                                                    @elseif($upload->plan->payday <= Carbon\Carbon::today() && $upload->plan->status)
                                                        <a class="badge badge-success" href="#">Paid</a>
                                                    @else
                                                        <a class="badge badge-info" href="#">Not Due</a>
                                                    @endif
                                                @endif
                                            @else
                                                Plan not found
                                            @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Data Unavailable</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <form action="{{route('staff.delete.all.repayments')}}"  method="post" id="repayment-form">
                            {{csrf_field()}}
                            <input type="hidden" id="repayments-delete-input" name="delete_repayments[]"/>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
    <script src="{{asset('assets/js/admin-custom.js')}}"></script>
@endsection