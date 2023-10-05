@extends('layouts.staff-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Recent Investors with no Loan funds</h4>
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    <div class="row">

        <div class="col-md-12">
            <table class="table table-responsive-sm table-hover table-outline mb-0" id="user-list" width="100%">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Reference</th>
                        <th>Investor Name</th>
                        <th>Wallet Balance</th>
                        <th>Signup Date</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                       
                       
                    </tr>
                </thead>

                <tbody>
                    @forelse($investors->sortByDesc('created_at') as $investor)
                        <tr>
                            <td>{{$loop->index+1}}</td>
                            <td>{{$investor->reference}}</td>
                            <td>{{$investor->name}}</td>
                            <td>₦{{number_format($investor->wallet, 2)}}</td>
                            <td>{{$investor->created_at}}</td>
                            <td>{{$investor->phone}}</td>
                            <td>{{$investor->email}}</td>
                            
                            
                        </tr>
                    @empty
                        <p>No User Found</p>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('page-js')
  <script>
    $('#user-list').DataTable( {
        responsive: true
    } );
  </script>
@endsection