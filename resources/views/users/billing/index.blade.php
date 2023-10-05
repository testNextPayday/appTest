@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Billing</li>

        <li class="breadcrumb-menu">
          <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-primary btn-lg waves-effect text-white" href="{{route('users.loan-requests.create')}}" style="border-radius: 20px;"> 
             <span style="font-size: 0.9rem;"> <i class="icon-cursor text-white"></i> Get Loan</span>
            </a>
          </div>
        </li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
            @if ($errors->any())
                <div class="row justify-content-center">
                    <div class="col-sm-6">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="row">
                <div class="col-md-12 mb-4">
                    <form method="post" action="{{ route('users.billing.add-card') }}" id="addCard" accept-charset="UTF-8">
                        {{ csrf_field() }}
                    </form>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Manage Billing Information</h4>
                            <button type="submit" form="addCard" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus"></i> ADD NEW
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            @foreach($cards->chunk(3) as $chunk)
                <div class="row">
                    @foreach($chunk as $card)
                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="icon-credit-card"></i>
                                        Last 4 Digits: <strong>{{$card->last4}}</strong>    
                                    </div>
                                    <a class="badge badge-danger p-1"
                                        href="{{ route('users.billing.remove-card', ['billingCard' => $card->id]) }}"
                                        onclick="return confirm('Are you sure?')">Remove</a>
                                </div>
                                <div class="card-body">
                                    <p>Card Type: <strong>{{ $card->card_type }}</strong></p>
                                    <p>Expiry Month: <strong>{{ $card->exp_month }}</strong></p>
                                    <p>Expiry Year: <strong>{{ $card->exp_year }}</strong></p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

    </div>
    <!-- /.conainer-fluid -->
    
    <div class="modal fade" id="addCard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Billing Medium (Card)</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
          
                <div class="modal-body">
                    <form method="POST" id="addCard" action="#" accept-charset="UTF-8">
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
    

</main>
@endsection

@section('page-js')

<script>
  function payWithPayant() {
    var handler = Payant.invoice({
        "key": "401eeb501677df88610d48a3cb25645c1d9b124e",
        "client": {
            "first_name": "{!! $user->firstName !!}",
            "last_name": "{!! $user->lastName !!}",
            "email": "{!! $user->email !!}",
            "phone": "{!! $user->phone !!}"
        },
        "due_date": "{{ now()->format('d/m/Y') }}",
        "fee_bearer": "client",
        "items": [
            {
                "item": "Card Setup fees",
                "description": "Card setup fees for {!! config('app.name') !!}}",
                "unit_cost": "450.00",
                "quantity": "1"
            }
        ],
        
        callback: function(response) {
            console.log(response);
        },
        onClose: function() {
            console.log('Window Closed.');
        }
    });

    handler.openIframe();
  }
</script>
@endsection