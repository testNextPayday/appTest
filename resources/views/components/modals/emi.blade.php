<div class="modal fade" id="emiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel-2">EMI Calculator</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                  
                    
                        $employers = App\Models\Employer::where('is_primary',false)->orWhere('is_primary',2)->orderBy('name')->get();
                       
          
                    
                ?>
                
                <emi-calculator :employers="{{$employers}}"
                    :collection-methods="{{ json_encode(config('settings.collection_methods')) }}">
                
                </emi-calculator>
            </div>
            <!--<div class="modal-footer">-->
            <!--    <button type="button" class="btn btn-success">Submit</button>-->
            <!--    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>-->
            <!--</div>-->
        </div>
    </div>
</div>