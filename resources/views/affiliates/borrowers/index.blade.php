@extends('layouts.affiliates')

@section('content')
@inject('carbon','Carbon\Carbon')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                Borrower Accounts
            </h4>
            <div>
                <div style="display:inline-block">
                    <form action="" method="get">
                        <input class="form-control" name="q" placeholder="Enter search term" value="{{$searchTerm}}" required/>
                    </form>
                </div>
                <a href="{{ route('affiliates.borrowers.create') }}" class="btn btn-info btn-sm">
                    <i class="fa fa-plus"></i>
                    Add new
                </a>
            </div>
        </div>
      
    </div>
    
    
    @include('layouts.shared.error-display')
    

    <div class="row" style="min-height: 70vh;">
        
        @foreach($borrowers as $borrower)
            <div class="col-md-4 grid-margin">
                <div class="card text-center">
                    <div class="card-body">
                        <img src="{{ $borrower->avatar }}" class="img-lg rounded-circle mb-2" alt="profile image" />
                        <h4>{{ $borrower->name }}</h4>
                        <p class="text-muted">{{ strtoupper($borrower->reference) }}</p>
                        <p class="text-muted">Payroll ID{{ strtoupper(optional($borrower->employments->first())->payroll_id) }}</p>
                        @if($borrower->is_active)
                            <button class="btn btn-sm btn-success mt-3">Active</button>
                        @else
                            <button class="btn btn-sm btn-danger mt-3">Inactive</button>
                        @endif
                        <a class="btn btn-info btn-sm mt-3"
                            href="{{ route('affiliates.borrowers.show', [ 'user' => $borrower->reference ]) }}">
                            View
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
        
        <div class="col-12 grid-margin">
            {{$borrowers->links('layouts.pagination.default')}} 
        </div>
        
    </div>

</div>
@endsection

@section('page-js')
@endsection