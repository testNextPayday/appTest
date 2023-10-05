<template>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Fund Wallet</h4>
           
        </div>
      
        <div class="card-body">
            <div class="form-group">
                    <label for="price">Amount</label>
                    <input type="number" 
                            name="price" 
                            v-model="amount"
                            id="price" class="form-control" placeholder="Enter Amount in Naira">
            </div>

            <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" v-model="startDate" name="start_date" id="start_date" placeholder="Enter Investment Start Date" required>
                        </div>

            <div class="form-group">
                            <label for="tenure">Tenure</label>
                            <select class="form-control" v-model="tenure" name="tenure" id="tenure" required>                                
                                <option value="6" >6 Months</option>
                                <option value="12">12 Months</option> 
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="interest_payment_cycle" class="form-control-label">Interest Payment Cycle</label>
                            <select name="interest_payment_cycle"  v-model="interestPaybackCycle" class="form-control" id="interest_payment_cycle" required>
                                <option title="Upfront" value="upfront" >Upfront</option>
                                <option title="Backend" value="backend">Backend</option>
                                <option title ="Monthly" value="monthly">Monthly</option>                                
                            </select>
                        </div>

                        <p><b>Amount: </b> ₦{{this.amount}} </p>
                        <p><b>Interest on Maturity: </b> ₦{{maturity_interest}} </p>
                        <p><b>Total Value: </b> ₦{{expected_profit}} </p>
        </div>
      
        <div class="card-footer">
            <div class="row"><p><small>Pay With Any of The Following</small></p></div>
            <div class="row">                
                <div class="col-md-4">
                    <button type="button" @click="monoPay" class="btn btn-info">Mono DirectPay</button>
                </div>
                <div class="col-md-4">
                    
                </div>
                <div class="col-md-4">
                    <button type="button" @click="fundWallet" class="btn btn-danger">
                    <i :class="spinClass"></i>{{buttonText}}</button>
                </div>
                
            </div>
        </div>
        
        <paystack ref="paystack" 
                :pay-key="payKey" 
                :email="email" 
                :amount="amount" @paystack-response="paystackResponse"></paystack>
      
    </div> 
</template>
<script>
    import Paystack from './../Paystack';
    import { utilitiesMixin } from './../../mixins';
    
    export default {
        name : 'payday-wallet-fund',
        mixins: [utilitiesMixin],
        
        props: ["payKey", "email", "upfront_tax","upfront_interest","monthly_tax","monthly_interest","backend_tax","backend_interest"
        ],
        
        data() {
            return {
                buttonText: 'Paystack',
                amount: 0,
                startDate: '',
                tenure:'',
                interestPaybackCycle:'' ,
                interest_cycle:'',
                interest_rate:'',
                tax_rate:'',
                interest_amount:''
            };
        },
        
        methods: {
            

           
            monoPay(){
                if (!this.amount || this.amount < 50000) {
                    this.alertError('Please provide an amount not less than 50,000.00');
                    return;
                }
                let data = {                    
                    amount: this.amount,
                    startDate: this.startDate,
                    tenure: this.tenure,
                    interestPaybackCycle:this.interestPaybackCycle
                };
                axios.post('/investors/promissory-notes/fund/payday/mono-fund', data).then(response => {
                    if(response.data.status === 1) {
                        //this.alertSuccess(response.data.paymentLink);
                        window.open(response.data.paymentLink);
                        
                    } else {
                        this.alertError(response.data.message);
                    }    
                    this.stopLoading();
                }).catch(error => {
                    this.alertError('An error occurred. Please try again');
                    this.stopLoading();
                });
            },

            fundWallet() {
                if (!this.amount || this.amount < 20000) {
                    this.alertError('Please provide an amount');
                    return;
                }
                this.startLoading();
                this.$refs.paystack.initiatePayment();
            },
            
            paystackResponse(event) {
                //console.log(event);
                this.buttonText = "Verifying...";
                let data = {
                    reference: event.reference, 
                    amount: this.amount,
                    startDate: this.startDate,
                    tenure: this.tenure,
                    interestPaybackCycle: this.interestPaybackCycle
                };
                axios.post('/investors/promissory-notes/fund/paystack/payments/verify', data).then(response => {
                    if(response.data.status === 1) {
                        this.alertSuccess(response.data.message);                        
                        element.modal("hide");
                        this.stopLoading();
                        
                    } else {
                        this.alertError(response.data.message);
                    }    
                    this.stopLoading();
                }).catch(error => {
                    this.alertError(error);
                    this.stopLoading();
                });
            },
        },

        computed:{
            expected_profit(){
                this.interest_cycle = this.interestPaybackCycle;
                if(this.interest_cycle == 'upfront'){
                    this.interest_rate = this.upfront_interest
                    this.tax_rate = this.upfront_tax                    
                }

                if(this.interest_cycle == 'backend'){
                    this.interest_rate = this.backend_interest
                    this.tax_rate = this.backend_tax
                }

                if(this.interest_cycle == 'monthly'){
                    this.interest_rate = this.monthly_interest
                    this.tax_rate = this.monthly_tax
                }
                
                this.interest_amount = ((this.amount * (this.interest_rate/100)) / 12) * this.tenure;
                this.tax_amount = (this.tax_rate / 100 ) * this.interest_amount;

                let expected_profit = parseFloat(this.amount) + parseFloat(this.interest_amount - this.tax_amount);
                expected_profit = parseFloat(expected_profit);                
                return typeof expected_profit === 'number' && !isNaN(expected_profit) && isFinite(expected_profit) ? this.round(expected_profit, 2) : 0;
                
            },

            maturity_interest(){
                this.interest_cycle = this.interestPaybackCycle;
                if(this.interest_cycle == 'upfront'){
                    this.interest_rate = this.upfront_interest
                    this.tax_rate = this.upfront_tax                    
                }

                if(this.interest_cycle == 'backend'){
                    this.interest_rate = this.backend_interest
                    this.tax_rate = this.backend_tax
                }

                if(this.interest_cycle == 'monthly'){
                    this.interest_rate = this.monthly_interest
                    this.tax_rate = this.monthly_tax
                }
                
                this.interest_amount = ((this.amount * (this.interest_rate/100)) / 12) * this.tenure;
                this.tax_amount = (this.tax_rate / 100 ) * this.interest_amount;

                let maturity_interest = parseFloat(this.interest_amount - this.tax_amount);
                maturity_interest = parseFloat(maturity_interest);                
                return typeof maturity_interest === 'number' && !isNaN(maturity_interest) && isFinite(maturity_interest) ? this.round(maturity_interest, 2) : 0;
                
            }
        },
        
        components: {
            'paystack': Paystack  
        },
    };
</script>

