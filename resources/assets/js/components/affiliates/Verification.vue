<template>
    <div>
        
        <!--<div v-if="paidVerificationFee || verificationDisabled" class="alert alert-info">-->
        <!--    <h4>Thank you for applying!</h4>-->
        <!--    <hr/>-->
        <!--    Please exercise some patience. You'll get access to your dashboard as soon-->
        <!--    as the ADMIN is done reviewing your application-->
        <!--</div>-->
        
        <div>
            <p>
                <strong>NB:</strong>&nbsp; Nextpayday is required to conduct due diligence on our affiliates.
                A verification fee of <strong>N {{verificationFee}}</strong> is charged for this service
                from your wallet.
                
                <span v-if="wallet < verificationFee">Please fund your wallet to proceed.</span>
            </p>
            
            <div class="text-right">
                
                <button v-if="wallet < verificationFee" type="button" class="btn btn-sm btn-info"
                    @click="goToGateway"
                    :disabled="loading">
                    <i :class="spinClass"></i>
                    {{payButtonText}}
                </button>
                
                <button v-else type="button" class="btn btn-sm btn-success"
                    @click="applyForVerification"
                    :disabled="loading">
                    <i :class="spinClass"></i>
                    {{ applyButtonText }}
                </button>
            </div>
                    
            <paystack ref="paystack" 
                :pay-key="payKey" 
                :email="email" 
                :amount="verificationFee"
                @paystack-response="paystackResponse"></paystack>
        </div>
        
    </div>
</template>

<script>
    import Paystack from './../Paystack';
    import { utilitiesMixin } from './../../mixins';
    
    export default {
        mixins: [utilitiesMixin],
        
        props: ['verificationFee', 'paidVerificationFee', 'verificationDisabled', 'email', 'payKey'],
        
        data() {
            return {
                payButtonText: 'Fund Wallet',
                applyButtonText: 'Apply for Verification',
            };
        },
        
        methods: {
            
            goToGateway() {
                this.startLoading();
                this.$refs.paystack.initiatePayment();
            },
            
            async applyForVerification() {
                
                try {
                    this.startLoading();
                    this.applyButtonText = "Applying";
                    
                    const response = await axios.post('/affiliates/verification-apply');
                    
                    this.alertSuccess(response.data.message);
                    
                    this.applyButtonText = "Application Successful!!";
                    
                    this.paidVerificationFee = true;
                    
                    this.stopLoading();
                    
                    
                } catch(e) {
                    this.handleApiErrors(e);
                    this.applyButtonText = "Apply for Verification";
                    this.stopLoading();
                }
            },
            
            paystackResponse(event) {
                this.payButtonText = "Verifying Payment";
                let data = {
                    reference: event.reference, 
                    amount: this.verificationFee
                };
                
                axios.post('/payments/wallet-fund/verify', data).then(response => {
                    if(response.data.status === 1) {
                        this.alertSuccess(response.data.message);
                        this.$store.dispatch('updateWallet', data.amount);
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
        
        computed: {
            wallet() {
                return this.$store.getters.getWallet;
            }
        },
        
        components: {
            'paystack': Paystack
        }
    };
</script>