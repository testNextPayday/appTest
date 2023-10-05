<template>


    <div class="row">

        <div class="col-md-12">

            <div class="card">
                <div class="card-header"> 
                    <h3 class="card-title">Success Rates</h3>
                </div>

                <div class="card-body">
                    <success-rate></success-rate>
                </div>
            </div>

        </div>



        <div class="col-md-12" style="height:40px;"></div>


        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Collection Analysis</h3>
                </div>
                <div class="card-body">
                    <collection-analysis></collection-analysis>
                </div>
            </div>
        </div>
        
    </div>

</template>

<script>
import CollectionSuccessRate from '../CollectionSuccessRates';
import CollectionAnalysis from '../CollectionAnalysis';
export default {
     components: {
      'success-rate' : CollectionSuccessRate,
      'collection-analysis': CollectionAnalysis
    },
}
</script>