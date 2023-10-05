@extends('layouts.admin-new')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
@section('content')
	<div class="content-wrapper">
		 <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">Loan Migration</h4>
            </div>
        </div>
        
		<div class="card">
			<div class="card-body">
			    <h4 class="card-title">Investor LoanFund Migration</h4>
				<loan-migration :investors="{{$investors}}"></loan-migration>
			</div>
		</div>
	</div>
@endsection

@section('page-js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
<!-- <script> $(function() {$('select').selectpicker(); }); </script> -->
@endsection