@extends('layouts.admin')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('admin.investors.index')}}">Investors</a></li>
        <!--<li class="breadcrumb-item active">All</li>-->
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
                            Investors
                            <div class="col-sm-3" style="display:inline-block">
                                <form action="" method="get">
                                    <input class="form-control" name="q" placeholder="Enter search term" value="{{$searchTerm}}" required/>
                                </form>
                            </div>
                            <div class="pull-right">
                                <a class="btn btn-success btn-sm" href="{{route('admin.investors.create')}}"><i class="fa fa-plus"></i> Add new</a>
                                &nbsp;
                                <a class="btn btn-primary btn-sm" href="{{route('admin.investors.apply')}}"><i class="fa fa-rocket"></i> Upgrade old</a>
                            </div>
                        </div>
                        <div id="dt_example" class="table-responsive card-body">
                            <table class="table table-responsive table-hover table-outline mb-0" id="data-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Name</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Reference</th>
                                        <th class="text-center">C. Rate</th>
                                        <th class="text-center">T. Rate</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($investors))
                                    @foreach($investors as $investor)
                                    <tr class="gradeA">
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
                                            {{$investor->email}}
                                        </td>
                                        <td class="text-center">
                                            {{$investor->reference}}
                                        </td>
                                        <td class="text-center">
                                            {{$investor->commission_rate}} %
                                        </td>
                                         <td class="text-center">
                                            {{$investor->tax_rate}} %
                                        </td>
                                        <td class="text-center">
                                            @if($investor->is_active)
                                            <button class="btn btn-sm btn-success">Active</button>
                                            @else
                                            <button class="btn btn-sm btn-warning">Disabled</button>
                                            @endif
                                        </td>
                                        
                                         <td class="text-center">
                                            <a href="{{route('admin.investors.view', ['investor' => $investor->reference])}}" class="btn btn-sm btn-info"><i class="icon-eye"></i> View</a>
                                            @if($investor->is_active)
                                            <a href="{{route('admin.investors.toggle', ['investor' => $investor->reference])}}" 
                                                onclick="return confirm('Are you sure you want to disable this investor?');"
                                                class="btn btn-sm btn-warning"><i class="icon-close"></i> Disable</a>
                                            @else
                                            <a href="{{route('admin.investors.toggle', ['investor' => $investor->reference])}}" 
                                                onclick="return confirm('Are you sure you want to enable this investor?');"
                                                class="btn btn-sm btn-success"><i class="icon-check"></i> Enable</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="7" class="text-right">
                                            {{$investors->links()}}
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            There are no investors here
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
@if(count($investors))
<script type="text/javascript">
    //Data Tables
    // $(document).ready(function () {
    //     $('#data-table').dataTable({
    //         "sPaginationType": "full_numbers",
    //         dom: 'Bfrtip',
    //         buttons: [
    //             'excelHtml5'
    //         ]
    //     });
    //     $("#data-table_length").css("display","none")
    // });
</script>
@endif
@endsection