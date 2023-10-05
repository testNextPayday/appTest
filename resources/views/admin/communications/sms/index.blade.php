@extends('layouts.admin-new')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">

@section('content')

    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">Communicate</h4>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center">
                <div class="card px-4 py-4" style="flex:1">
                    <form action="{{ route('admin.communications.sms') }}" method="post" class="">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6 mb-2">

                               <div>
                                <label for="borrowers">Search borrowers</label>
                                <select type="text" class="form-control selectpicker" name="borrowersearch[]" id="borrowers" data-live-search="true" multiple onchange="chooseactive()">
                                    @foreach($users as $user)
                                        <option value="{{$user->phone}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                               </div>
                               <div class="text-center mb-2 mt-2">OR</div>
                               <div>
                                 <label for="allborrowers">
                                    <input type="checkbox" name="borrowers" id="allborrowers" onclick="chooseactive()" />
                                        Send to all borrowers
                                 </label>
                               </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div>
                                <label for="investors">Search investors</label>
                                <select type="text" class="form-control selectpicker" name="investorsearch[]" id="investors" data-live-search="true" multiple onchange="chooseactive()">
                                    @foreach($investors as $investor)
                                        <option value="{{$investor->phone}}">{{$investor->name}}</option>
                                    @endforeach
                                </select>
                                </div>
                                <div class="text-center mb-2 mt-2">OR</div>
                                <div>
                                    <label for="investors">
                                        <input type="checkbox" name="investors" id="allinvestors" onclick="chooseactive()"/>
                                        Send to all investors
                                    </label>
                                </div>
                            </div>

                           
                        </div>

                        <div class="row">


                            <div class="col-md-4">
                            
                                <label for="mandatory_sms">
                                    <input type="radio" name="sender" id="mandatory_sms" value="N-Alert"/>
                                    N-ALERT SMS(by passes DND)
                                </label>
                            </div>

                            <div class="col-md-4">
                            
                                <label for="nextpayday_sms">
                                    <input type="radio" name="sender" id="nextpayday_sms" checked value="Nextpayday"/>
                                    Nextpayday SMS(does not bypass DND)
                                </label>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <textarea class="form-control" rows="10"
                                    name="message" placeholder="Enter message" required>{{ old('message') }}</textarea>
                            </div>    
                        </div>
                        <button class="btn btn-primary btn-sm btn-rounded">Send Message</button>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
   
@endsection

@section('page-js')
<script type="text/javascript">
    function chooseactive() {
       
        if ($("#allborrowers").is(":checked")) {
            $("#allborrowers").attr("disabled", false);
            $("#allinvestors").attr("disabled", true);
            $("#investors").attr("disabled", true);
            $("#borrowers").attr("disabled", true);
        }else if($("#allinvestors").is(":checked")){
            $("#allinvestors").attr("disabled", false);
            $("#allborrowers").attr("disabled", true);
            $("#investors").attr("disabled", true);
            $("#borrowers").attr("disabled", true);
        }else if($("select[name='investorsearch[]'] option:selected").length > 0){
            $("#investors").attr("disabled", false);
            $("#borrowers").attr("disabled", true);
            $("#allinvestors").attr("disabled", true);
            $("#allborrowers").attr("disabled", true);
        }else if($("select[name='borrowersearch[]'] option:selected").length > 0){
            $("#borrowers").attr("disabled", false);
            $("#investors").attr("disabled", true);
            $("#allinvestors").attr("disabled", true);
            $("#allborrowers").attr("disabled", true);
        }else{
            $("#allborrowers").attr("disabled", false);
            $("#allinvestors").attr("disabled", false);
            $("#investors").attr("disabled", false);
            $("#borrowers").attr("disabled", false);
        }
   }

</script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
 <script> $(function() {$('select').selectpicker(); }); </script>
@endsection
