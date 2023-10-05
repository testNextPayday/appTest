@extends('layouts.investor')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Dashboard</h4>
        </div>
    </div>
          
    @component('components.investor-stats', ['investor' => $investor])
    @endcomponent

    @if ($investor->isPromissory())
      
        <div class="row">
            <div class="col-md-7">
                <h5>Good day, {{$investor->name}}</h5>
            </div>
            <div class="col-md-5 grid-margin stretch-card">

            </div>
        </div>
        <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
                <div class="tab-pane fade show active pr-3 " id="user-profile-info" role="tabpanel" aria-labelledby="user-profile-info-tab">                                        
                    <div class="row ">
                        <div class="col-12 mt-5">
                            <h5>Basic Information</h5>
                        </div>
                    </div>
                    
                    <div class="table-responsive ">
                        <table class="table table-borderless w-100 mt-4 ">
                            <tr>
                                <td>
                                    <strong>Full Name :</strong> {{ $investor->name }}
                                </td>
                                <td>
                                    <strong>Phone :</strong> {{ $investor->phone }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Email :</strong> {{ $investor->email }}
                                </td>
                                <td>
                                    <strong>Location :</strong> {{ $investor->address }}                                                    
                                </td>
                            </tr>
                        </table>
                    </div>
            
                    <div class="row ">
                        <div class="col-12 mt-5">
                            <h5 class="mb-5 ">Other Information</h5>
                            <div class="stage-wrapper pl-4">
                                <div class="stages border-left pl-5 pb-4">
                                    <div class="btn btn-icons btn-rounded stage-badge btn-inverse-success">
                                            <i class="mdi mdi-texture "></i>
                                    </div>
                                    <div class="d-flex align-items-center mb-2 justify-content-between">
                                        <h5 class="mb-0 ">Bank Details</h5>
                                    </div>
                                    @if($investor->bank)
                                    <p>Bank Name: {{ $investor->bank->bank_name }}</p>
                                    <p>Bank Code: {{ $investor->bank->bank_code }}</p>
                                    <p>Account Number: {{ $investor->bank->account_number }}</p>
                                    @else
                                    Bank details unavailable
                                    @endif
                                </div>
                                <div class="stages border-left pl-5 pb-4 ">
                                    <div class="btn btn-icons btn-rounded stage-badge btn-inverse-danger">
                                        <i class="mdi mdi-download "></i>
                                    </div>
                                    <div class="d-flex align-items-center mb-2 justify-content-between ">
                                        <h5 class="mb-0 ">Documents</h5>
                                    </div>
                                    <p>Manage your Nextpayday Documents</p>
                                    <div class="file-icon-wrapper">
                                        @if($investor->getOriginal('licence'))
                                        <a href="{{$investor->licence}}" target="_blank">
                                            <div class="btn btn-outline-danger file-icon">
                                                <i class="mdi mdi-file-pdf "></i>
                                            </div>
                                        </a>
                                        @endif
                                        @if($investor->getOriginal('reg_cert'))
                                        <a href="{{$investor->reg_cert}}" target="_blank">
                                            <div class="btn btn-outline-primary file-icon">
                                                <i class="mdi mdi-file-word"></i>
                                            </div>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="stages pl-5 pb-4">
                                    <div class="btn btn-icons btn-rounded stage-badge btn-inverse-primary">
                                        <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                                    </div>
                                    <div class="d-flex align-items-center mb-2 justify-content-between">
                                        <h5 class="mb-0">Phone Verification</h5>
                                    </div>
                                    <p>Verify your phone number.</p>
                                    <p><em>* This service is currently unavailable</em></p>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>

            <div class="col-md-5 grid-margin stretch-card">

            </div>
        
        </div>
        <!-- Modal -->
  <div class="modal fade" id="npdmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="text-muted" id="exampleModalCenterTitle">Invest Now</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="conainer-fluid">
            <div class="row">
              <div class="col-md-7">
                <img src="/images/popup2.svg">
              </div>
               <div class="col-md-5">
                <h3>Get Started</h3>
                <p>Fund Your Wallet Today. </p>
                <a href="{{ route('investors.promissory-note.fund.account')}}">
                  <button type="button" class="btn btn-pill btn-outline-primary">Invest</button>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    @endif
</div>
        <!-- content-wrapper ends -->


@endsection

@section('page-js')
<script src="{{asset('coreui/js/views/main.js')}}"></script>
<script type="text/javascript">
    $(window).on('load',function(){
        $('#npdmodal').modal('show');
    });
</script>
<script>

     var toggleMenu = function() {
        var body = $('body');
        if ((body.hasClass('sidebar-toggle-display')) || (body.hasClass('sidebar-absolute'))) {
            body.toggleClass('sidebar-hidden');
        } else {
            body.toggleClass('sidebar-icon-only');
        }
     }
</script>
@endsection