<template>
    <div>
        <button @click="pay">Pay</button>
        <paystack ref="paystack" :pay-key="payKey" 
                :reference="reference" 
                :email="email" 
                :amount="amount" @paystack-response="paystackResponse"></paystack>
    </div>
</template>

<script>
    import Paystack from './Paystack';
    
    export default {
        props: ['payKey', 'reference', 'email', 'amount'],
        
        components: {
            'paystack': Paystack
        },
        
        methods: {
            pay() {
                this.$refs.paystack.initiatePayment();  
            },
            paystackResponse(event) {
                console.log(event);
            }
        }
    };
</script>