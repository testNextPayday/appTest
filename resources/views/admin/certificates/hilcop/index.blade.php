@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Hilcop Certificates</a></li>
        <li class="breadcrumb-item active">Investments</li>
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
                            Investment Certificates
                            <span class="pull-right">
                                <a href="{{route('admin.hilcop-certificates.investments.new')}}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i>
                                    &nbsp; New Certificate
                                </a>
                            </span>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Recipient</th>
                                        <th>Cert. Number</th>
                                        <th class="text-center">No. of Shares</th>
                                        <th class="text-center">Value per share</th>
                                        <th class="text-center">Membership Date</th>
                                        <th>Certificate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($certificates))
                                    @foreach($certificates as $certificate)
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            <div>{{$certificate->name}}</div>
                                        </td>
                                        <td>
                                            <div>{{$certificate->reference}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                            {{$certificate->no_of_shares}} 
                                            </div>
                                        </td>
                                        
                                        <td class="text-center">
                                            <div class="small text-muted">
                                               ₦ {{number_format($certificate->value_per_share, 2)}} 
                                            </div>
                                        </td>
                                       
                                        
                                        
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                {{$certificate->membership_date}}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <a class="btn btn-primary btn-sm " target="_blank" href="{{$certificate->certificate}}"> View </a>
                                                <a class="btn btn-danger btn-sm " onclick="event.preventDefault();document.getElementById('delete-{{$certificate->reference}}').submit()" href="{{route('admin.hilcop-certificates.investments.delete',['id'=>$certificate->id])}}" > Delete </a>

                                                <form method="POST" id="delete-{{$certificate->reference}}" action="{{route('admin.hilcop-certificates.investments.delete',['id'=>$certificate->id])}}">@csrf</form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="9" class="text-right">
                                            {{$certificates->links()}}
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            No certificates have been issued yet
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