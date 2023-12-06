@extends('layouts.admin-new')
@section('content')
<div class="content-wrapper">
    <div class="card">
    @include('flash-message')       
        <div class="card-body">
                <h2 class="card-title"> Savings settings</h2>
            <div class="row mt-5">
                <div class="col-md-6">
                    <form method="post" action="{{route('admin.savings.update')}}">
                        @csrf
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="">Interest</label>
                                    <input type="text" name="interest_percent" class="form-control" value="{{$savings_settings->interest_percent}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">Savings Minimum Amount</label>
                                    <input type="text" name="minimum_savings" class="form-control" value="{{$savings_settings->minimum_savings}}">
                                </div>
                                <div class="form-group col-md-6">
                                <label for="">Savings   Minimum Duration</label>
                                    <input type="text" name="minimum_duration" class="form-control" value="{{$savings_settings->minimum_duration}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-3 mb-0">
                                    <button type="submit" class="btn btn-primary ">Submit</button>
                                </div>
                                <div class="form-group col-xl-9">
                                </div>
                            </div>    
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')


<script src="{{asset('assets/js/data-table.js')}}">
</script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>

@endsection