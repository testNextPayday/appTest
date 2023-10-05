<template>
    <span>{{wallet}}</span>
</template>
<script>
    export default {
        computed: {
            wallet() {
                return this.$store.getters.getWallet;
            }
        }  
    };
</script>