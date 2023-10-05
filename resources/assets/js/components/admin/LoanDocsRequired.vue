<template>
	<div class="row">
      <div class="col-md-12">
        <div class="card">
	      <h5 class="card-header">LoanRequest settings</h5>
	    	<div class="card-body">
    		  <form class="form" method="post">

    			<div class="form-group">
    				<label for="bank">Bank Statement/Payslip</label>
    				<input type="checkbox" name="payslip" id="bank" class="form-control" v-model="loandocs.bank_statement">
    			</div>

				<div class="form-group">
					<label>Manual loan authorized</label>
					<input type="checkbox" name="manualoan" id="manualoan" class="form-control" v-model="loansettings.manual_loan">
				</div>

				<div class="form-group">
					<label>Disable Capitalize</label>
					<input type="checkbox" name="is_capitalized" id="is_capitalized" class="form-control" v-model="capitalizeOn" >
				</div>

				<div class="form-group">
					<label>Enable Upgrade</label>
					<input type="checkbox" name="upgrade_enabled" id="upgrade_enabled" class="form-control" v-model="upgradeOn">
				</div>

				<div class="form-group">
					<label>Enable Upfront Interest</label>
					<input type="checkbox" name="upfront_interest" id="upfront_interest" class="form-control" v-model="upfrontInterestOn">
				</div>

				<div class="form-group">
					<label>Settle Affiliate On Repayment </label>
					<input type="checkbox" name="affiliate_payment_method" id="affiliate_payment_method" class="form-control" v-model="affillaiteRepaymentOn">
				</div>

    			<!-- <div class="form-group">
    				<label for="payslip"></label>
    				<input type="checkbox" name="payslip" id="payslip" class="form-control" v-model="loandocs.payslip">
    			</div> -->

    			<div class="form-group">
    				<button type="button" class="btn btn-success float-right" :disabled="loading" v-on:click="submitForm($event)"><i :class="buttonClass"></i>Save</button>
    			</div>

    		  </form>
	    	</div>
        </div>
      </div>
    </div>
</template>
<script>
import { utilitiesMixin } from "../../mixins";
export default {
	props:['loandocs', 'loansettings', 'user_id', 'capitalize', 'upgrade', 'repayment', 'upfrontinterest'],

	mixins:[utilitiesMixin],

	data(){
		return{
			loading: false,
			capitalizeOn : this.capitalize,
			upgradeOn : this.upgrade,
			upfrontInterestOn : this.upfrontinterest,
			affillaiteRepaymentOn : this.repayment,
            buttonClass: {
                fa: true,
                "fa-check-circle-o": true,
                "fa-spin": false,
                "fa-spinner": false
            },
		}
	},

	methods:{
		submitForm: function(event){
		    this.startLoading();
		 	axios.post('/ucnull/employers/loanRequest/docs/settings/'+this.user_id, {
				 loandocs: this.loandocs,
				 loansettings: this.loansettings,
				 capitalize: this.capitalizeOn,
				 upgrade: this.upgradeOn,
				 upfrontinterest: this.upfrontInterestOn,
				 repayment: this.affillaiteRepaymentOn
				})
            .then(response => {
				// console.log(response);
                this.stopLoading();
                this.alertSuccess("Updated loanRequest document settings.");
			}).catch(error => {
                this.stopLoading();
                this.alertError(error);
			})

		},

	    startLoading() {
	        this.loading = true;
	        this.buttonClass['fa-spinner'] = true;
	        this.buttonClass['fa-spin'] = true;
	        this.buttonClass['fa-check-circle-o'] = false;
	    },
	    
	    stopLoading() {
	        this.loading = false;
	        this.buttonClass['fa-spinner'] = false;
	        this.buttonClass['fa-spin'] = false;
	        this.buttonClass['fa-check-circle-o'] = true;
	    }
	}
};
</script>