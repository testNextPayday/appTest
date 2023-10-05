<template>
    <span>
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>INFORMATION
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="closeModal">&times;</button>
                    </span>
                </h4>
            </div>
    
            <div slot="body">
                <div>
                    <p>
                        This employer is <b>unverified</b>. If this employer is your current employer,
                        you will not be able to place loan requests. You can shoot us a mail to verfify
                        this employer. This, however, usually takes some time. To speed up the process,
                        you can pay a verification fee of ₦ {{verificationFee}}.
                    </p>                 
                </div>
            </div>
    
            <div slot="footer">
                <div>
                    <button class="btn btn-info" :disabled="loading" @click="closeModal">
                        <i class="fa fa-close"></i>
                        Close
                    </button>
                    <button class="btn btn-info pull-right" :disabled="loading" @click="makePayment">
                        <i :class="buttonClass"></i>
                        {{buttonText}}
                    </button>
                </div>
            </div>
        </modal> 
        
        <paystack ref="paystack" 
                :pay-key="payKey" 
                :email="email" 
                :amount="verificationFee" @paystack-response="paystackResponse"></paystack>
    </span>
    
</template>
<script>
    import Paystack from './Paystack';
    export default {
        props: ["show", "verificationFee", "wallet", "email", "payKey", "employerId"],
        
        data() {
            return {
                loading: false,
                error_message: '',
                buttonClass: {
                    fa: true,
                    "fa-check-circle-o": true,
                    "fa-spin": false,
                    "fa-spinner": false
                },
                buttonCloseClass: {
                    fa: true,
                    "fa-close": true,
                    "fa-spin": false,
                    "fa-spinner": false
                },
                showModal: false,
                buttonText: 'Pay Verification Fee'
            };
        },
        
        methods: {
            makePayment() {
                if(this.verificationFee > this.wallet) {
                    let goToGateway = confirm("You don't have enough money in your wallet. Proceed to gateway?");
                    if(goToGateway) {
                        this.goToGateway();
                    }
                } else {
                    //debit from wallet
                    this.debitFromWallet();
                }
            },
            
            debitFromWallet() {
                this.startLoading();
                let data = {
                    employer_id: this.employerId,
                    amount: this.verificationFee
                };
                axios.post('/profile/employer/verify-payment-wallet', data).then(response => {
                    if(response.data.status === 1) {
                        this.$emit('employer-verification-request-placed', response.data);
                    } else {
                        this.setError(response.data.message);
                    }    
                    this.stopLoading();
                }).catch(error => {
                    this.setError('An error occurred. Please try again');
                    this.stopLoading();
                });
            },
            
            goToGateway() {
                this.startLoading();
                this.$refs.paystack.initiatePayment();
            },
            
            paystackResponse(event) {
                this.buttonText = "Verifying Payment";
                let data = {
                    reference: event.reference, 
                    employer_id: this.employerId,
                    amount: this.verificationFee
                };
                axios.post('/profile/employer/verify-payment', data).then(response => {
                    if(response.data.status === 1) {
                        this.$emit('employer-verification-request-placed', response.data);
                    } else {
                        this.setError(response.data.message);
                    }    
                    this.stopLoading();
                }).catch(error => {
                    this.setError('An error occurred. Please try again');
                    this.stopLoading();
                });
            },
            
            closeModal() {
                this.showModal = false;
                this.$emit('modal-closed', false);
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
            },
            
            setError(message) {
                this.error_message = message;
            },
            
            clearError() {
                this.error_message = '';  
            }
        },
        
        components: {
            'paystack': Paystack  
        },
        
        watch: {
            "show": function(current) {
                this.showModal = current;
            }
        }
    }
</script>