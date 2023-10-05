<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Funds for this Request ({{$loanRequest->funds()->count()}})</h4>
                        <table class="table table-responsive-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Investor Reference</th>
                                    <th>Investor Name</th>
                                    <th class="text-center">Offer</th>
                                    <th>Fund Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loanRequest->funds()->latest()->get() as $fund)
                                    <tr>
                                        <td>
                                            <div>
                                                {{$fund->investor->reference}}
                                             </div>
                                        </td>
                                        <td>
                                            <div>{{$fund->investor->name}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-muted">
                                                ₦ {{$fund->amount}} <span>({{$fund->percentage}}%)</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$fund->created_at->toDateString()}}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            There are no funds for this Request
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <!--/.col-->
        </div>