@extends('layouts.investor')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Promissory Notes</a></li>
        <li class="breadcrumb-item active">Active</li>
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
                            Promissory Notes
                        </div>
                        <div class="card-body">
                            <table  id="order-listing" class="table table-responsive table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th><i class="icon-credit-card"></i></th>
                                        <th>Cert. Number</th>
                                        <th>Amount</th>
                                        <th>Current Value</th>
                                        <th>Status</th>
                                        <th>Maturity Date</th>
                                        <th>Start Date</th>
                                        <th>Certificate</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse($promissoryNotes as $note)
                                        @php($investor = $note->investor)
                                        <tr>
                                            <td>{{$loop->index + 1}}</td>
                                            <td><a href="{{route('investors.promissory-note.view', ['promissory_note'=>$note->reference])}}" target="_blank">{{$note->reference}}</a></td>
                                            <td>{{number_format($note->principal, 2)}}</td>
                                            <td>{{$note->current_value}}</td>
                                            <td>
                                                @if($note->status == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @endif

                                                @if($note->status == 2)
                                                    <span class="badge badge-danger">Active</span>
                                                @endif

                                            </td>
                                            <td>{{$note->maturity_date}}</td>
                                            <td>{{$note->start_date}}</td>
                                            <td>
                                                <a class="btn btn-primary btn-sm btn-block" target="_blank" href="{{$note->certificateUrl}}"> View </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <p>No active promissory note available yet</p>
                                    @endforelse
                                    
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