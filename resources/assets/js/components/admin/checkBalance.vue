<template>

    <div v-if="loading">
        <newton-loader></newton-loader>
    </div>
     <form @submit.prevent="checkBalance" v-else>

        <p>Your Paystack Account Balance : <span v-if="balanceAmount == 0">----------</span>  <span v-else>₦{{formatAsCurrency(balanceAmount)}}</span></p>

        <button type="submit" name="submit" class="btn btn-xs btn-primary">Check Balance</button>

    </form>

</template>

<script>

import { utilitiesMixin } from './../../mixins';

export default {
    
    mixins: [utilitiesMixin],

    data(){

        return {

            balanceAmount : 0
        };
    },

    methods : {

         async checkBalance(){

            this.startLoading();

            await axios.get('/ucnull/transfer-controls/check/balance').then((res)=>{
                
                this.alertSuccess('Balance retrieved');

                this.balanceAmount = res.data[0].balance;

            }).catch((e)=>{

               this.alertError(e.response.data)
            })

            this.stopLoading();
        }
    }
}
</script>