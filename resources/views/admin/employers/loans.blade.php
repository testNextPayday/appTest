@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">{{ $employer->name }}'s Employees Loans</h4>
            <a class="btn btn-sm btn-primary"
                href="{{ route('admin.employers.view', ['employer_id' => encrypt($employer->id)]) }}">
                <i class="fa fa-arrow-left"></i> {{ $employer->name }}
            </a>
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                {{-- <div class="card-header d-flex align-items-center justify-content-between"> --}}
                    {{-- <a href="{{ route('admin.sweep.employer', ['employer' => $employer->id]) }}" class="btn btn-info btn-sm"> --}}
                    {{--    Sweep {{$employer->name}}'s Loans --}}
                    {{-- </a> --}}
                {{-- </div> --}}
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table id="order-listing" class="table">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Borrower Email</th>
                                        <th>Phone</th>
                                        <th>Date Borrowed</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loans as $loan)
                                        @php($borrower = $loan->user)
                                        <tr>
                                            <td>
                                                <a href="{{route('admin.loans.view', ['reference' => $loan->reference])}}">
                                                    {{$loan->reference}}
                                                </a>
                                            </td>
                                            <td>₦{{number_format($loan->amount, 2)}}</td>
                                            <td>{{optional($borrower)->email}}</td>
                                            <td>{{optional($borrower)->phone}}</td>
                                            <td>{{$loan->created_at }}</td>
                                            <td>{{$loan->due_date }}</td>
                                            <td class="text-center">
                                                <a href="{{route('admin.loans.view', ['reference' => $loan->reference])}}" class="badge badge-primary">
                                                    View Loan
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No Loans</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
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