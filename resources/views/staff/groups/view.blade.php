@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('staff.group.create')}}">Create Group</a></li>
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
                            Groups
                            <div class="col-sm-3" style="display:inline-block">
                                <form action="" method="get">
                                    <input class="form-control" name="q" placeholder="Enter search term" value="{{$searchTerm}}" required/>
                                </form>
                            </div>                            
                        </div>
                        <div id="dt_example" class="table-responsive card-body">
                            <table class="table table-responsive table-hover table-outline mb-0" id="data-table">
                                <thead class="thead-light">
                                    <tr class="col-md-12">
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Name</th>
                                        <th>Group Reference</th>                                                           
                                        <th class="text-center">Date Created</th>
                                    </tr>
                                </thead>
                                <tbody class="col-md-12">
                                    @if(count($groups))
                                    @foreach($groups as $group)
                                    <tr class="gradeA">
                                        <td>
                                            <div>{{$loop->index+1}}</div>
                                        </td>
                                        <td>
                                            <div>{{$group->name}}</div>
                                        </td>
                                        <td class="text-center">
                                            {{$group->reference}}
                                        </td>
                                        <td class="text-center">
                                            {{$group->created_at}}
                                        </td>
                                        
                                         <td class="text-center">
                                            <a href="{{route('staff.group.single-view', ['group' => $group->reference])}}" class="btn btn-sm btn-info"><i class="icon-eye"></i> View</a>
                                            
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="7" class="text-right">
                                            {{$groups->links()}}
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            There are no groups here
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
@if(count($groups))
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