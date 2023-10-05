@extends('layouts.admin-new')

@section('content')
<div class="content-wrapper">
    @if(session('skippedUrl'))
    <div class="alert alert-danger">
        <a href="{{route('skipped.repayments.download')}}">Download Skipped Repayments</a>
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="card">
        <div class="card-body">

            <bulk-repayment url="{{route('admin.bulk-repayment')}}"></bulk-repayment>
            @component('components.upload_excel_repayment')
            @endcomponent
        </div>
    </div>
</div>
@endsection

@section('page-js')
<!--<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>-->
<!--<script src="{{asset('assets/js/admin-custom.js')}}"></script>-->
<script type="text/javascript">
    $('#add-payment').click(function(e) {
        e.preventDefault();

        $("#repayments-table").each(function() {

            var tds = '<tr>';
            jQuery.each($('tr:last td', this), function() {
                tds += '<td>' + $(this).html() + '</td>';
            });
            tds += '</tr>';
            if ($('tbody', this).length > 0) {
                $('tbody', this).append(tds);
            } else {
                $(this).append(tds);
            }
        });
    });
</script>

@endsection