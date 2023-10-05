<div class="modal fade" id="settlement-opts" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel-2">Choose Settlement Option</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('admin.loan-requests.update')}}" >
                    {{csrf_field()}}
                    
                    <button type="submit" class="btn btn-success btn-big">Pay Online</button>
                   
                </form>
            </div>
            <!--<div class="modal-footer">-->
            <!--    <button type="button" class="btn btn-success">Submit</button>-->
            <!--    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>-->
            <!--</div>-->
        </div>
    </div>
</div>