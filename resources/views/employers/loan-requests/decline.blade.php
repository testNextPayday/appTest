@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Decline Loan Application</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @include('layouts.shared.error-display')
                    
                    <br/>
                    <form method="post" action="{{ route('employers.loan-requests.decline') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="code" value="{{ $code }}"/>
                        <input type="hidden" name="reference" value="{{ $reference }}"/>
                        <div class="form-group">
                            <label>Reason for Declining Loan Request</label>
                            <textarea class="form-control" name="decline_reason" required rows="5"
                                placeholder="Please tell us why you are declining this loan application">
                                {{ old('decline_reason') }}
                            </textarea>
                        </div>
                        <br/>
                        <button class="btn btn-danger btn-sm">Decline Request</button>
                    </form>
                    <br/>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
