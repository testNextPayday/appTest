<button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#addLoanTransaction">
    <i class="fa fa-plus"></i> Transact on Wallet
</button>
@if(request()->user('admin'))
<button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#zeroriseWallet">
    <i class="fa fa-minus"></i> Zerorise Wallet
</button>
@endif
<div class="modal fade" id="addLoanTransaction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{$loan->user->name}} Wallet Transaction(s) </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
               <transact-wallet :reference='"{{$loan->reference}}"' :user='"{{$loan->user->reference}}"'></transact-wallet>
            </div>
           
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="zeroriseWallet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">If you proceed, this customer's wallet balance will be equated to ₦0.00 </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.loans.zerorise.wallet',['reference'=>$loan->user->reference])}}" method="POST" style="display:inline">
                    @csrf
                    <button class="btn btn-success">Continue</button>
                </form>
            </div>
           
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>