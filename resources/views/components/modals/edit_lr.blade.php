<div class="modal fade" id="edit_loan_request_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel-2">Edit Loan Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{$url}}">
                    {{csrf_field()}}
                    <input type="hidden" name="reference" value="{{$loanRequest->reference}}">
                    <div class="form-group row">
                        <label for="request_amount" class="col-md-4">Request Amount</label>
                        <input type="text" class="form-control col-md-8" name="amount" value="{{$loanRequest->amount}}">
                    </div>
                    <div class="form-group row">
                        <label for="request_duration" class="col-md-4">Request Duration</label>
                        <input type="text" class="form-control col-md-8" name="duration" value="{{$loanRequest->duration}}">
                    </div>
                    <div class="form-group row">
                        <label for="interest_rate" class="col-md-4">Interest Rate</label>
                        <input type="text" class="form-control col-md-8" name="interest_percentage" value="{{$loanRequest->interest_percentage}}">
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success">Save</button>
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