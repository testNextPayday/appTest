@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Certificates</a></li>
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
                                <a href="{{route('admin.certificates.investments.new')}}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i>
                                    &nbsp; New Certificate
                                </a>
                            </span>
                        </div>
                        <div class="card-body">
                            <table  id="order-listing" class="table table-responsive table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th><i class="icon-credit-card"></i></th>
                                        <th>Recipient</th>
                                        <th>Cert. Number</th>
                                        <th>Amount</th>
                                        <th>Rate</th>
                                        <th>Tax</th>
                                        <th>Maturity Date</th>
                                        <th>Action</th>
                                        <th>Certificate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($certificates))
                                    @foreach($certificates as $certificate)
                                    <tr>
                                        <td>
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            <div>{{$certificate->name}}</div>
                                        </td>
                                        <td>
                                            <div>{{$certificate->reference}}</div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                ₦ {{number_format($certificate->amount, 2)}}
                                            </div>
                                        </td>

                                        <td>
                                            <div class="small text-muted">
                                                {{$certificate->rate}}
                                            </div>
                                        </td>

                                        <td>
                                            <div class="small text-muted">
                                                {{$certificate->tax}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$certificate->maturity_date}}
                                            </div>
                                        </td>



                                        <td>
                                            <div>
                                                <a class="btn btn-primary btn-sm btn-success" data-toggle="modal" href="#" data-target="#{{$certificate->reference}}"> Details </a>
                                                
                                                <div class="modal fade" id="{{$certificate->reference}}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Certificate Details</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                               <div class="row">
                                                                   <div class="col-md-12">
                                                                       <div class="row">
                                                                           <div class="col-md-6">
                                                                               <b>Acct. Name:</b> 
                                                                           </div>
                                                                           <div class="col-md-6">
                                                                                {{$certificate->account_name}}
                                                                           </div>


                                                                           <div class="col-md-6">
                                                                               <b>Acct. Number:</b> 
                                                                           </div>
                                                                           <div class="col-md-6">
                                                                           {{$certificate->account_number}}
                                                                           </div>

                                                                           <div class="col-md-6">
                                                                               <b>Bank:</b>
                                                                           </div>
                                                                           <div class="col-md-6">
                                                                           {{$certificate->bank}}
                                                                           </div>


                                                                           <div class="col-md-6">
                                                                               <b>Phone:</b>
                                                                           </div>
                                                                           <div class="col-md-6">
                                                                           {{$certificate->phone}}
                                                                           </div>

                                                                         

                                                                        

                                                                           

                                                                       </div>
                                                                   </div>
                                                               </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <a class="btn btn-primary btn-sm btn-block" target="_blank" href="{{$certificate->certificate}}"> View </a>
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
                                        <td colspan="9">
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

    <script src="{{asset('assets/js/data-table.js')}}">
    </script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
   
@endsection