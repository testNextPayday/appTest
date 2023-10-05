<template>
    
</template>
<script>
    export default {
        props: ['user'],
        
        mounted() {
            this.setUserWallet();  
            this.setUserEscrow();
        },
        
        methods: {
            
            setUserWallet() {
                this.$store.dispatch('updateWallet', this.user.wallet);
            },
            
            setUserEscrow() {
                this.$store.dispatch('updateEscrow', this.user.escrow);
            },
        }  
    };
</script>