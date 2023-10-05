<button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#addLoanTransaction">
    <i class="fa fa-plus"></i> Add Transaction
</button>
<div class="modal fade" id="addLoanTransaction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Transaction </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{$url}}">
                    @csrf
                    <input type="hidden" name="loan_id" value="{{$loan->id}}">
                    <div class="form-group">
                        <label for="loan_ref" class="form-control-label">Loan Reference</label>
                        <input type="text" name="loan_reference" class="form-control" disabled value="{{$loan->reference}}">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label"> Transaction Name</label>
                        <input type="text" class="form-control" name="transaction_name">
                    </div>
                   
                    <div class="form-group">
                        <label class="form-control-label">Select Transaction Type</label>
                        <select class="form-control" name="type">
                            <optgroup label="Debit Transactions">
                                <option value="1">Cash Debit</option>
                            </optgroup>
                            <optgroup label="Credit Transactions">
                                <option value="2">Cash Credit</option>
                            </optgroup>
                        </select>

                    </div>
                    <div class="form-group">
                        <label for="amount" class="form-control-label">Amount</label>
                        <input type="text" name="amount" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Description (optional)</label>
                        <textarea class="form-control" placeholder="Describe the transaction a bit" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" value="Add" class="btn btn-primary">
                    </div>
                </form>
            </div>
           
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>