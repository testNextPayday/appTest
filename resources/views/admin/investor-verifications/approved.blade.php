@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">Investors Activation</a></li>
        <li class="breadcrumb-item active">Approved</li>
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
                            Approved Requests
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Investor</th>
                                        <th>Reference</th>
                                        <th>Tax Number</th>
                                        <th>Managed</th>
                                        <th>Status</th>
                                        <th>ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($requests))
                                    @foreach($requests as $req)
                                    <tr>
                                       <td>
                                            <div><a href="{{route('admin.investors.view', ['investor' => $req->investor->reference])}}">{{$req->investor->name}}</a></div>
                                        </td>
                                        <td>
                                           {{$req->reference}}
                                        </td>
                                        
                                        <td>
                                            {{$req->tax_number}}
                                        </td>
                                        
                                        <td>
                                            {{$req->managed_account ? 'Yes' : 'No'}}
                                        </td>
                                        
                                        <td>
                                            @if ($req->status == 2)
                                            Pending
                                            @elseif($req->status == 1)
                                            Approved
                                            @else
                                            Declined
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <a target="_blank" href="{{$req->licence}}" class="btn btn-sm btn-success">View ID</a>
                                        </td>
                                        
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="6" class="text-right">
                                            {{$requests->links()}}
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            There are no approved requests
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