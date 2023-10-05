<template>
    <div>
        <div class="form-group">
            <label for="referrer"> <b>Pick a Referrer &nbsp;&nbsp; </b></label>
            <label class="active">
              <input type="radio" name="referral_method" value="1" v-model="referralMethod"> Enter Affiliate Code
            </label>
           
            <label class="">
              <input type="radio" name="referral_method" value="2" v-model="referralMethod"> Select a borrower
            </label>
            
        </div>
         <div class="form-group" v-if="referralMethod == '1'">
            <label for="aff_code" style="display:block">Affiliate Referrer <small>Not Required</small></label>
            <input type="text" class="form-control" name="affiliate_code"  :v-model="code" placeholder="" >
        </div>

        <div class="form-group" v-else-if="referralMethod == '2'">
            <label for="reference">Referring Borrowers</label>
            <select class="form-control selectpicker" name="affiliate_code"  data-live-search="true">
                <option v-for="user in users" :key="user.id" :value="user.reference">{{user.reference+' '+user.name}}</option>
            </select>
        </div>

        <div v-else>
            <label>No affiliate method selected</label>
        </div>

        
    </div>
</template>

<script>
export default {

    props : {
        users : {
            type : Array,
            required : true
        },

        code : {
            type : String,
            required : true
        }
    },

    watch : {
        referralMethod : function(val) {

            if (val == '2') {

                $('.bootstrap-select').first().show();
            }else {

                $('.bootstrap-select').first().hide();
            }
        }
    },
    data() {

        return {
            referralMethod : '2'
        };
    }

}

</script>