<template>
    
</template>
<script>
    export default {
        props: ['payKey', 'reference', 'email', 'amount'],
        
        methods: {
            initiatePayment(){
                let paymentdata = {};
                paymentdata.key = this.payKey;
                paymentdata.email = this.email;
                // Adds payment charges to customer
                paymentdata.amount = parseInt((this.amount * 100)  + (this.amount * 1.5) + (100 * 100));
                if(this.reference) {
                    paymentdata.reference = this.reference;
                }
                paymentdata.callback = this.onCallback;
                paymentdata.onClose = this.onClose();
                
                var handler = PaystackPop.setup(paymentdata);
                handler.openIframe();
            },
            
            onCallback(response) {
                this.$emit('paystack-response', response);
            },
            
            onClose() {
                this.$emit('paystack-modal-close', 'modal closed');
            }
        }
    }
</script>