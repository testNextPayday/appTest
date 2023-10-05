@extends('layouts.staff-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Recent Users with no Loan Request</h4>
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    <div class="row">

        <div class="col-md-12">
            <table class="table table-responsive table-hover table-outline mb-0" id="user-list" width="100%">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Reference</th>
                        <th>Action</th>
                        <th>Staff Assigned</th>
                        <th>Borrower Name</th>
                        <th>Signup Date</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Gender</th>
                       
                    </tr>
                </thead>

                <tbody>
                    @forelse($users->sortByDesc('created_at') as $user)
                    @php
                        if ($user->adder_type == 'AppModelsInvestor') {
                            $referrer = 'No Staff';
                        }else {
                            $referrer = $user->referrer->name;
                        }
                    @endphp
                        <tr class="{{$user->taken_up == 1 ? 'underlined' : '' }}">
                            <td>{{$loop->index+1}}</td>
                            <td>{{$user->reference}}</td>
                            <td>
                               @if($user->taken_up == 0)
                                <a href="{{route('staff.takeup.users', ['user'=> $user->reference])}}" class="btn btn-xs btn-inverse-success">Take Up</a>
                               @else 
                                <span class="badge badge-busy"> Taken Already</span>
                               @endif
                            </td>
                            <td>{{$referrer}}</td>
                            <td><a href="{{route('staff.accounts.view', ['user'=> $user->reference])}}">{{$user->name}}</a></td>
                            <td>{{$user->created_at}}</td>
                            <td>{{$user->phone}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->genderDesc}}</td>
                            
                        </tr>
                    @empty
                        <p>No User Found</p>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>

    
</style>

@endsection

@section('page-js')
  <script>
    $('#user-list').DataTable( {
        responsive: true
    } );
  </script>
@endsection