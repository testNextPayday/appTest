<template>
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Fund Wallet</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
      
        <div class="modal-body">
            <input type="number" 
                    name="price" 
                    v-model="amount"
                    id="price" class="form-control" placeholder="Enter Amount in Naira">
        </div>
      
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" @click="fundWallet" class="btn btn-danger">
                <i :class="spinClass"></i>{{buttonText}}</button>
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
        mixins: [utilitiesMixin],
        
        props: ["payKey", "email"],
        
        data() {
            return {
                buttonText: 'Fund',
                amount: ''
            };
        },
        
        methods: {
            fundWallet() {
                if (!this.amount) {
                    this.alertError('Please provide an amount');
                    return;
                }
                this.startLoading();
                this.$refs.paystack.initiatePayment();
            },
            
            paystackResponse(event) {
                this.buttonText = "Verifying...";
                let data = {
                    reference: event.reference, 
                    amount: this.amount
                };
                
                axios.post('/payments/wallet-fund/verify', data).then(response => {
                    if(response.data.status === 1) {
                        this.alertSuccess(response.data.message);
                        this.$store.dispatch('updateWallet', data.amount);
                        let element = window.$("#fundWallet");
                        if (element) {
                            element.modal("hide");
                        }
                    } else {
                        this.alertError(response.data.message);
                    }    
                    this.stopLoading();
                }).catch(error => {
                    this.alertError('An error occurred. Please try again');
                    this.stopLoading();
                });
            },
        },
        
        components: {
            'paystack': Paystack  
        },
    };
</script>