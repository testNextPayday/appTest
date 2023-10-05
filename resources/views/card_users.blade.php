<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Card userse</title>
	<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
</head>
<body>
	<table id="myTable">
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
                            <form action="{{route('card.try')}}" method="POST">
                            	{{csrf_field()}}
                            	<input type="text" name="plan">
                            	<button type="submit">Submit</button>
                            </form>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
	$(document).ready( function () {
    $('#myTable').DataTable();
} );
</script>
</html>