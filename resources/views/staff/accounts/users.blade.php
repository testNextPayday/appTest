@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Staff</a></li>
        <li class="breadcrumb-item active">Accounts</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
            @if ($errors->any())
                <div class="row justify-content-center">
                    <div class="col-sm-6">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Managed Accounts
                            <div class="col-sm-3" style="display:inline-block">
                                <form action="" method="get">
                                    <input class="form-control" name="q" placeholder="Enter search term" value="{{$searchTerm}}" required/>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Name</th>
                                        <th class="text-center">User Type</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($users))
                                    @foreach($users as $user)
                                    <tr>
                                        <td class="text-center">
                                            <div class="avatar">
                                                <img src="{{$user->avatar}}" class="img-avatar" alt="avatar">
                                                <span class="avatar-status badge-success"></span>
                                             </div>
                                        </td>
                                        <td>
                                            <div>{{$user->name}}</div>
                                        </td>
                                        <td class="text-center">
                                            $user->reference
                                        </td>
                                        <td class="text-center">
                                            @if($user->is_active)
                                            <button class="btn btn-sm btn-success">Active</button>
                                            @else
                                            <button class="btn btn-sm btn-warning">Disabled</button>
                                            @endif
                                        </td>
                                        
                                         <td class="text-center">
                                            <a href="{{route('staff.accounts.view', ['user_id' => encrypt($user->id)])}}" class="btn btn-sm btn-info"><i class="icon-eye"></i> View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="5" class="text-right">
                                            {{$users->links()}}
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            You've not gotten an account to manage yet
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            <!--/.row-->
        </div>

    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')
@endsection