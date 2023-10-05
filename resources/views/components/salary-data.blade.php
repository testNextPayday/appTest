@if ($gotValidData)
    <div class="row justify-content-center">
        <div class="col-sm-5">
            <ul class="list-group">
                <li class="list-group-item">CUSTOMER DATA</li>
                <li class="list-group-item"><small>Customer Name:</small> <strong>{{$salaryData->data->customerName}}</strong></li>
                <li class="list-group-item list-group-item-primary"><small>Remita ID:</small> <strong>{{$salaryData->data->customerId}}</strong></li>
                <li class="list-group-item list-group-item-secondary"><small>Company Name:</small> <strong>{{$salaryData->data->companyName}}</strong></li>
                <li class="list-group-item list-group-item-success"><small>Account Number:</small> <strong>{{$salaryData->data->accountNumber}}</strong></li>
                <li class="list-group-item list-group-item-danger"><small>Bank Code:</small> <strong>{{$salaryData->data->bankCode}}</strong></li>
                <li class="list-group-item list-group-item-warning"><small>First Payment Date:</small> <strong>{{Carbon\Carbon::parse($salaryData->data->firstPaymentDate)->toDateString()}}</strong></li>
            </ul>
        </div>
        <div class="col-sm-5">
            <table class="table table-responsive-sm table-hover table-outline mb-0">
                <thead class="thead-light">
                    <tr><th colspan="2" class="text-center">SALARY HISTORY</th></tr>
                    <tr>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaryData->data->salaryPaymentDetails as $data)
                    <tr>
                        <td>
                            <div class="text-center">₦ {{$data->amount}}</div>
                        </td>
                        <td>
                            <div class="text-muted text-center">
                                {{Carbon\Carbon::parse($data->paymentDate)->toDateString()}}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center">
                            Salary Information Unavailable
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>  
        </div>
    </div>
@else
    
    <div class="row justify-content-center">
        <div class="col-sm-6">
            <div class="alert alert-danger">
                <span class="fa fa-warning"></span>&nbsp;
                {{ $salaryData ? $salaryData->responseMsg : 'Data could not be fetched!'}}
            </div>
        </div>
    </div>
    
@endif