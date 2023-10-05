<template>
    <span>{{escrow}}</span>
</template>
<script>
    export default {
        computed: {
            escrow() {
                return this.$store.getters.getEscrow;
            }
        }  
    };
</script>