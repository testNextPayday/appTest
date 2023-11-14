<table  id="order-listing" class="table table-responsive table-hover table-outline mb-0">
    <thead class="thead-light">
        <tr>
            <th><i class="icon-credit-card"></i></th>
            <th>Recipient</th>
            <th>Cert. Number</th>
            <th>Amount</th>
            <th>Current Value</th>
            <th>Status</th>
            <th>Payment Type</th>
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
                <td><a href="{{route('admin.investors.view', ['investor'=>$investor->reference])}}" target="_blank">{{$investor->name}}</a></td>
                <td><a href="{{route('admin.promissory-notes.view', ['promissory_note'=>$note->reference])}}" target="_blank">{{$note->reference}}</a></td>
                <td>{{number_format($note->principal, 2)}}</td>
                <td>{{$note->current_value}}</td>
                <td>
                    @component('components.promissory-note-status', ['note'=> $note])
                    @endcomponent
                </td>
                <td>{{$note->payment_type}}</td>
                <td>{{Carbon\Carbon::parse($note->maturity_date)->modify('-1 months')->format('d-m-Y')}}</td>
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