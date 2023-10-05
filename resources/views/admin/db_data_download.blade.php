<table class="align-middle mb-4 table table-bordered table-striped transactions-table ">
    <thead>
        <tr>
            <th class="text-center">Name</th>
            <th class="text-center">Email</th>
            <th class="text-center">Date</th>
        </tr>
    </thead>
    <tbody>
       

        @foreach ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at }}</td>
        </tr> 
        @endforeach                                   
    </tbody>
</table>