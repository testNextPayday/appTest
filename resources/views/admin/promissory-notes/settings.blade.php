@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Promissory Notes Settings</a></li>
        <!-- <li class="breadcrumb-item active">Active</li> -->
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
                            <span class="pull-right">
                                <a href="#" data-toggle="modal" data-target="#create-settings" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i>
                                    &nbsp; Create Note Settings
                                </a>
                            </span>
                        </div>
                        <div class="card-body">

                            <table  id="order-listing" class="table table-responsive table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th><i class="icon-credit-card"></i></th>
                                        <th>Name</th>
                                        <th>Interest Rate</th>
                                        <th>Tax Rate</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse($settings as $setting)
                                        
                                        <tr>
                                            <td>{{$loop->index + 1}}</td>
                                            <td>{{$setting->name}}</td>
                                            <td>{{$setting->interest_rate}} %</td>
                                            <td>{{$setting->tax_rate}}</td>
                                            <td>
                                                <button class="btn btn-primary " data-toggle="modal" data-target="#edit{{$setting->id}}"><i class="fa fa-edit"></i> Edit</button>


                                                <div class="modal fade" id="edit{{$setting->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel-2">Edit Promissory Note Settings</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST" action="{{route('admin.promissory-settings.update', ['note_setting'=> $setting->id])}}">
                                                                    {{csrf_field()}}

                                                                    <div class="form-group row">
                                                                        <label for="name" class="col-md-4">Name</label>
                                                                        <input type="text" class="form-control col-md-8" name="name" value="{{$setting->name}}">
                                                                    </div>
                                                                
                                                                    <div class="form-group row">
                                                                        <label for="interest_rate" class="col-md-4">Interest Rate</label>
                                                                        <input type="text" class="form-control col-md-8" name="interest_rate" value="{{$setting->interest_rate}}">
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label for="tax_rate" class="col-md-4">Tax Rate</label>
                                                                        <input type="text" class="form-control col-md-8" name="tax_rate" value="{{$setting->tax_rate}}">
                                                                    </div>

                                                                    <div class="form-group text-center">
                                                                        <button type="submit" class="btn btn-success">Updateb Settings</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <!--<div class="modal-footer">-->
                                                            <!--    <button type="button" class="btn btn-success">Submit</button>-->
                                                            <!--    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>-->
                                                            <!--</div>-->
                                                        </div>
                                                    </div>
                                                </div>

                                                <form style="display:inline" method="POST" action="{{route('admin.promissory-settings.delete', ['note_setting'=> $setting->id])}}">
                                                    @csrf
                                                    <button class="btn btn-danger "><i class="fa fa-trash"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <p>No promissory note settings available yet</p>
                                    @endforelse
                                    
                                </tbody>
                            </table>



                        </div>

                        <div class="modal fade" id="create-settings" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel-2">Create Promissory Note Settings</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="{{route('admin.promissory-settings.store')}}">
                                            {{csrf_field()}}

                                            <div class="form-group row">
                                                <label for="name" class="col-md-4">Name</label>
                                                <input type="text" class="form-control col-md-8" name="name" value="">
                                            </div>
                                        
                                            <div class="form-group row">
                                                <label for="interest_rate" class="col-md-4">Interest Rate</label>
                                                <input type="text" class="form-control col-md-8" name="interest_rate" value="">
                                            </div>
                                            <div class="form-group row">
                                                <label for="tax_rate" class="col-md-4">Tax Rate</label>
                                                <input type="text" class="form-control col-md-8" name="tax_rate" value="">
                                            </div>

                                            <div class="form-group text-center">
                                                <button type="submit" class="btn btn-success">Create Settings</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!--<div class="modal-footer">-->
                                    <!--    <button type="button" class="btn btn-success">Submit</button>-->
                                    <!--    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>-->
                                    <!--</div>-->
                                </div>
                            </div>
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