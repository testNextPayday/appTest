<div class="card">
    
    <div class="card-body">
        
        <div class="row">

            <div class="col-md-12">
                
                <form method="POST" action="{{route('admin.dissolve-penalty', ['loan'=>$loan->reference])}}" style="display:inline">
                    @csrf
                    <input type="submit" class="btn btn-xs btn-danger" value="Dissolve Penalties">
                </form>
                <br><br>
            </div>

            <div class="col-md-12">
                
                <treat-penalty :reference="'{{$loan->reference}}'"></treat-penalty>
            </div>
            
            

        </div>
    </div>
</div>

