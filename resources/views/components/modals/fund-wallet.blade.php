<div class="modal fade" id="fundWallet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Fund Account Wallet</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
      
            <div class="modal-body">
                <form method="POST" id="fundAccountWallet" action="{{ route('admin.users.fund-wallet') }}" accept-charset="UTF-8">
                    <div class="form-group">
                        <label>Account Reference</label>
                        <input type="text" name="reference" class="form-control" placeholder="Enter account reference" required>    
                    </div>
                    
                    <div class="form-group">
                        <label>Payment code</label>
                        <input type="text" name="code" class="form-control" placeholder="Enter Payment Code" required>    
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Amount</label>
                        <input type="number" name="amount" class="form-control" placeholder="Enter Amount" required>    
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="fundAccountWallet" class="btn btn-primary">Fund Wallet</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
    
    