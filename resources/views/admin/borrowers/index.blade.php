@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">Borrowers</a></li>
        <li class="breadcrumb-item active">All</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
            
            @include('layouts.shared.error-display')
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Users
                            <div class="col-sm-3" style="display:inline-block">
                                <form action="" method="get">
                                    <input class="form-control" name="q" placeholder="Enter search term" value="{{$searchTerm}}" required/>
                                </form>
                            </div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#newUser">
                                    <i class="fa fa-plus"></i>
                                    Add new
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Name</th>
                                        <th>Email</th>
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
                                            {{trim($user->name)}}
                                        </td>
                                        <td>
                                            {{$user->email}}
                                        </td>
                                        <td class="text-center">
                                            @if($user->is_active)
                                            <button class="btn btn-sm btn-success">Active</button>
                                            @else
                                            <button class="btn btn-sm btn-warning">Disabled</button>
                                            @endif
                                        </td>
                                        
                                         <td class="text-center">
                                            <a href="{{route('admin.users.view', ['user' => $user->reference])}}"
                                                class="btn btn-sm btn-info">
                                                <i class="icon-eye"></i> View
                                            </a>
                                            @if($user->is_active)
                                            <a href="{{route('admin.users.toggle', ['user_id' => encrypt($user->id)])}}" 
                                                onclick="return confirm('Are you sure you want to disable this user?');"
                                                class="btn btn-sm btn-warning"><i class="icon-close"></i> Disable</a>
                                            @else
                                            <a href="{{route('admin.users.toggle', ['user_id' => encrypt($user->id)])}}" 
                                                onclick="return confirm('Are you sure you want to enable this user?');"
                                                class="btn btn-sm btn-success"><i class="icon-check"></i> Enable</a>
                                            @endif
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
                                            There are no registered users
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

        <div class="modal fade" id="newUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add new user</h4><br>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
              
                    <form method="post" action="{{route('admin.users.store')}}">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="input-group mb-3">
                            <span class="input-group-addon"><i class="icon-question"></i></span>
                            <select name="is_company" class="form-control" required>
                                <option value="1">Company</option>                                
                                <option value="0">Individual</option>                                
                            </select>
                        </div>
                        
                        <div class="input-group mb-3">
                            <span class="input-group-addon"><i class="icon-user"></i></span>
                            <input type="text" name="name" class="form-control" required placeholder="Company/Individual Name">
                        </div>
                        
                        <div class="input-group mb-3">
                            <span class="input-group-addon">@</span>
                            <input type="email" name="email" class="form-control" required placeholder="Email">
                        </div>
                        
                        <div class="input-group mb-3">
                            <span class="input-group-addon"><i class="icon-phone"></i></span>
                            <input type="number" name="phone" class="form-control" required placeholder="Phone">
                        </div>
                    </div>
              
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Add</button>
                    </div>
                    </form>
              
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')
@endsection