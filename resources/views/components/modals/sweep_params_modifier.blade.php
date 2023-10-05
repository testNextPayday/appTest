@php
    switch(get_class($model)) {
        case "App\Models\Loan":
            $updateRoute = route('admin.loans.sweep-params', ['loan' => $model->reference]);
            break;
        case "App\Models\Employer":
            $updateRoute = route('admin.employers.sweep-params', ['employer' => $model->id]);
            break;
        default:
            $updateRoute = "#";
    }
@endphp

<div class="modal fade" id="updateCollectionParams" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Sweep Parameters</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
      
            <div class="modal-body">
                <form method="POST" id="updateCollectionParamsForm" action="{{ $updateRoute }}" accept-charset="UTF-8">
                    <div class="form-group">
                        <label>Sweep Start Day</label>
                        <input type="number" name="sweep_start_day" class="form-control"
                            placeholder="Enter Sweep Start Day" required
                            value="{{ old('sweep_start_day') ?? $model->sweep_start_day }}">    
                    </div>
                    <div class="form-group">
                        <label>Sweep End Day</label>
                        <input type="number" name="sweep_end_day" class="form-control"
                            placeholder="Enter Sweep End Day" required
                            value="{{ old('sweep_end_day') ?? $model->sweep_end_day }}">    
                    </div>
                    <div class="form-group">
                        <label>Sweep Frequency</label>
                        <input type="number" name="sweep_frequency" class="form-control"
                            placeholder="Enter Sweep Frequency" required
                            value="{{ old('sweep_frequency') ?? $model->sweep_frequency }}">    
                    </div>
                    <div class="form-group">
                        <label>Peak Start Day</label>
                        <input type="number" name="peak_start_day" class="form-control"
                            placeholder="Enter Peak Start Day" required
                            value="{{ old('peak_start_day') ?? $model->peak_start_day }}">    
                    </div>
                    <div class="form-group">
                        <label>Peak End Day</label>
                        <input type="number" name="peak_end_day" class="form-control"
                            placeholder="Enter Peak End Day" required
                            value="{{ old('peak_end_day') ?? $model->peak_end_day }}">    
                    </div>
                    <div class="form-group">
                        <label>Peak Frequency</label>
                        <input type="number" name="peak_frequency" class="form-control"
                            placeholder="Enter Peak Frequency" required
                            value="{{ old('peak_frequency') ?? $model->peak_frequency }}">    
                    </div>
                    
                    {{ csrf_field() }}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="updateCollectionParamsForm" class="btn btn-primary">Update Sweep Parameters</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
    
    