<hr>
<div style="height:30px;"></div>
<form method="POST" action="{{route('admin.excel.repayments')}}" enctype="multipart/form-data" class="row">
     @csrf
     <div class="form-group col-md-4">
         <label class="form-control-label">Upload Excel File</label>
         <input type="file" name="repayments" class="form-control-file">
     </div>
     <input type="hidden" name="type" value="DDAS">

     <div class="form-group col-md-4">
        <label  class="form-control-label">Primary Employer</label>
        <select name="employer_id" class="form-control">
            @foreach(App\Models\Employer::primary()->get() as $employer)
                <option value="{{$employer->id}}">{{$employer->name}}</option>
            @endforeach
        </select>
     </div>


     <div class="form-group col-md-4">
         <label for="" class="form-control-label">.</label>
         <input type="submit" name="submit" class="btn btn-primary" value="Upload Sheet">
     </div>
 </form>
