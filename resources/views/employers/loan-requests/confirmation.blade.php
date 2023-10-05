@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-{{ $status ? 'success' : 'danger' }}">
                <div class="panel-heading">Confirmation</div>

                <div class="panel-body">
                    <div class="alert alert-{{ $status ? 'success' : 'danger' }}">
                        {{ $message }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
