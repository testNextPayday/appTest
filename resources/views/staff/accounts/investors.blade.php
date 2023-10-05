@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Staff</a></li>
        <li class="breadcrumb-item active">Investor Accounts</li>
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
                            Managed Investor Accounts
                            <div class="col-sm-3" style="display:inline-block">
                                <form action="" method="get">
                                    <input class="form-control" name="q" placeholder="Enter search term" value="{{$searchTerm}}" required/>
                                </form>
                            </div>
                            <div class="pull-right">
                                <a class="btn btn-success btn-sm" href="{{route('staff.investors.create')}}"><i class="fa fa-plus"></i> Add new</a>
                                &nbsp;
                                <a class="btn btn-primary btn-sm" href="{{route('staff.investors.apply')}}"><i class="fa fa-rocket"></i> Apply </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Name</th>
                                        <th class="text-center">Reference</th>
                                        <th class="text-center">Verification</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($investors))
                                    @foreach($investors as $investor)
                                    <tr>
                                        <td class="text-center">
                                            <div class="avatar">
                                                <img src="{{$investor->avatar}}" class="img-avatar" alt="avatar">
                                                <span class="avatar-status badge-success"></span>
                                             </div>
                                        </td>
                                        <td>
                                            <div>{{$investor->name}}</div>
                                        </td>
                                        <td class="text-center">
                                            {{$investor->reference}}
                                        </td>
                                        <td class="text-center">
                                            @if($investor->is_verified)
                                                <button class="btn btn-block btn-sm btn-success">Verified</button>
                                            @elseif($investor->hasPendingVerification())
                                                <button class="btn btn-block btn-sm btn-warning">Pending</button>
                                            @else
                                                <a class="btn btn-sm btn-block btn-danger"
                                                    href="{{route('staff.investors.apply', ['reference' => $investor->reference])}}">Apply</a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($investor->is_active)
                                            <button class="btn btn-sm btn-block btn-success">Active</button>
                                            @else
                                            <button class="btn btn-sm btn-block btn-danger">Inactive</button>
                                            @endif
                                        </td>
                                        
                                         <td class="text-center">
                                            <a href="{{route('staff.accounts.investors.view', ['investor' => $investor->reference])}}" class="btn btn-sm btn-info"><i class="icon-eye"></i> View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="6" class="text-right">
                                            {{$investors->links()}}
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            You've not gotten a lender account to manage yet
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