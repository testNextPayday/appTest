<div class="modal fade" id="withdrawalRequestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel-2">Request Funds</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="withdrawalForm" method="post"
                    action="{{ route('affiliates.withdrawals.store') }}">
                    @csrf
                    <div class="form-group">
                        <label>Enter Amount</label>
                        <input type="text" class="form-control" name="amount" required 
                            placeholder="Enter withdrawal amount">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" form="withdrawalForm" class="btn btn-success">Submit Request</button>
            </div>
        </div>
    </div>
</div>