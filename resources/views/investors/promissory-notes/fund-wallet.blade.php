@extends('layouts.investor')

@section('content')
<main class="main">
  <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('users.loan-requests.index')}}">Promissory Note</a></li>
        <li class="breadcrumb-item active">Fund Account</li>     
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row justify-content-center">              
                <div class="col-sm-6">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                                $upfrontTaxRate = App\Models\PromissoryNoteSetting::upfrontTaxRate();
                                $upfrontInterestRate = App\Models\PromissoryNoteSetting::upfrontInterestRate();

                                $monthlyTaxRate = App\Models\PromissoryNoteSetting::monthlyTaxRate();
                                $monthlyInterestRate = App\Models\PromissoryNoteSetting::monthlyInterestRate();

                                $backendTaxRate = App\Models\PromissoryNoteSetting::backendTaxRate();
                                $backendInterestRate = App\Models\PromissoryNoteSetting::backendInterestRate(); 
                            ?>
                            <payday-wallet-fund
                                :email="'{{Auth::guard('investor')->user()->email}}'"
                                :upfront_tax="{{$upfrontTaxRate}}"
                                :upfront_interest="{{$upfrontInterestRate}}"
                                :monthly_tax="{{$monthlyTaxRate}}"
                                :monthly_interest="{{$monthlyInterestRate}}"
                                :backend_tax="{{$backendTaxRate}}"
                                :backend_interest="{{$backendInterestRate}}"
                                :pay-key="'{{config('paystack.publicKey')}}'">
                            </payday-wallet-fund>               
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br>
</main>
@endsection

@section('page-js')

<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> 
<script type="application/javascript" src="https://connect.withmono.com/connect.js"></script>


@endsection