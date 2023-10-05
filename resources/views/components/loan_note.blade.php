
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <!-- <button class="float-right"><i class="fa fa-toggle-down" data-toggle="collapse" data-target="#notes"  ></i></button> -->
                <h4 class="card-title">Loan Notes</h4>
            </div>

            <div class="card-body">
                <form class="" method="POST" action="{{route('app.loan-notes.store')}}">
                    @csrf
                    <input type="hidden" name="loan_id" value="{{$loan->id}}">
                    <div class="form-group">
                        <label class="form-control-label">Drop a Note about this loan</label>
                        <textarea class="form-control" name="note"></textarea>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary " type="submit"> Save </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
    
   <div class="col-12" id="notes">

        @forelse ($notes->sortByDesc('created_at') as $note)
            @php
                $editable = $user == $note->owner;

                if ($editable == '') {
                    $editable = 0;
                }
                $url = route('app.loan-notes.update', ['note'=> $note->id]);
                $deleteUrl = route('app.loan-notes.delete', ['note'=> $note->id]);
                $ownerName = optional($note->owner)->name; 
            @endphp
            <loan-note :url="'{{$url}}'" :editable="{{$editable}}" :token="'{{csrf_token()}}'" :deletetoken="'{{csrf_token()}}'" :note="'{{$note->note}}'" :deleteurl="'{{$deleteUrl}}'" :owner="'{{$ownerName}}'"></loan-note>
        @empty
            <p>There is no loan note for this loan</p>
        @endforelse
   </div>


</div>