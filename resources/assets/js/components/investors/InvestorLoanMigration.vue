<template>
	<form @submit.prevent="handleSubmit">
		<div class="row">
			<div class="col">
				Transfer From:
				<label for="from">Select Investor</label>
				<select type="text" class="form-control" v-model="from" @change="onSelectedFrom" id="from" data-live-search="true">
					<option v-for="investor in investors" :value="investor">{{investor.name}}</option>
				</select>
			</div>

			<div class="col">
				 Transfer To:
				<label for="to">Select Investor</label>
				<select type="text" class="form-control" v-model="to" id="to" data-live-search="true">
					<option v-for="investor in investors" :value="investor">{{investor.name}}</option>
				</select>
			</div>
		</div>

		<div class="row mt-2">
			<div class="col-6">
				<ul class="list-group">
					<li class="list-group-item" v-for="investorLoan in investorLoanFunds" v-if="investorLoanFunds != 0">
						<input type="checkbox" :value="investorLoan" v-model="selectedLoanFund" @change="getSelectedFundCurrentValue">
						{{investorLoan.reference}} <span>(₦{{investorLoan.amount.toLocaleString()}})</span><span>({{investorLoan.created_at}})</span>
					</li>
					<h5 class="mt-3 text-danger" v-if="investorLoanFunds.length == 0"> Investors LoanFund Not Found !! </h5>
				</ul>
			</div>

			<div class="col-6" v-if="to != ''">
				<div class="mt-3 mr-3">
					<h4 class="card-title">Ref: {{to.reference}}</h4>
					<p class="card-title">Name: {{to.name}}</p>

					<h4 class="card-title">Wallet balance: <span class="text-info">₦{{to.wallet.toLocaleString()}}</span></h4>
					<h4 class="card-title">Total Amount: <span class="text-info">₦{{formatAsCurrency(loanFundCurrentValue)}}</span></h4>
				</div>
			</div>
		</div>

		<div class="mt-3">
			<button class="btn btn-info float-right" :disabled="loading"><i :class="buttonClass"></i>Migrate</button>
		</div>
	</form>
</template>

<script>
import { utilitiesMixin } from './../../mixins';

export default {
	mixins: [utilitiesMixin],

	props: ['investors'],

	data(){
		return {
			investorLoanFunds:[],
			to: "",
			from: "",
			selectedLoanFund:[],
			loading: false,
			buttonClass: {
                fa: true,
                "fa-check-circle-o": true,
                "fa-spin": false,
                "fa-spinner": false
            },
            loanFundCurrentValue: 0
		};
	},

	computed:{
	
	},

	methods:{
		onSelectedFrom(){
			this.selectedLoanFund = []; // reset computed onchange of an investor
			return this.investorLoanFunds = this.from.loan_funds;
		},

		getSelectedFundCurrentValue(){
			axios.get('/ucnull/loans/investor/loanFund/currentValue', {
				params:{
					selectedLoanFund: JSON.stringify(this.selectedLoanFund),
				}
			}).then((res) => {

				this.loanFundCurrentValue=res.data;
			}).catch((err) => {

				this.alertError(err);
			});
		},
	 	async handleSubmit(){
	 	   if (!this.to) return this.alertError("Provide an investor to receive");
	 	   if (!this.from) return this.alertError("Provide an investor ");
	 	   if (this.investorLoanFunds.length == 0) return this.alertError("No LoanFund Found!");
	 	   if (this.loanFundCurrentValue == 0) return this.alertError("Selected loanFund value is null.");
	 	   if (this.loanFundCurrentValue > this.to.wallet) return this.alertError("Insufficient wallet balance for migration");

	 	   try{
	 	   	this.startLoading();
	 	   	 const response = await axios.post('/ucnull/loans/investor/loanFund/migrate', {
	 	   	 	to: JSON.stringify(this.to),
	 	   	 	from: JSON.stringify(this.from),
	 	   	 	loanFundCurrentValue: JSON.stringify(this.loanFundCurrentValue),
	 	   	 	selectedLoanFund: JSON.stringify(this.selectedLoanFund)
	 	   	 });
	 	   	this.alertSuccess("Migration Successful");
        	this.stopLoading();
	 	   }catch(e){
	 	     this.alertError(e);
	 	   	 this.stopLoading();
	 	   }
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
	},

};
</script>