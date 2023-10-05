<template>
     <form method="post" id="requestForm" :action="url" @submit="submit">
       
         <input type="hidden" name="_token" :value="token">
        <div class="form-group">
            <label class="">Max Amount : <b>₦ {{maxWithdrawal}}</b></label>
            <label class="">Current Balance : <b>₦ {{Intl.NumberFormat().format(this.wallet)}}</b></label>
            <label class="" :class="{'text-danger':errMsg.length > 0 }">Remaining : <b>₦ {{Intl.NumberFormat().format(this.wallet - this.amount)}}</b></label>
        </div>
        <div class="form-group">
            <label for="amount">Enter Amount</label>
            <input type="number" class="form-control" id="amount" placeholder="Enter amount you want to withdraw" name="amount" required value="" v-model="amount">
            <small class='alert alert-danger' v-if="errMsg.length" style="font-size:65%;padding:6px 7px;">{{errMsg}}</small>
        </div>
      
    </form>
</template>

<script>
    export default {
        name : 'withdrawal-request-form',
        props : ['url','limit','token','wallet'],
        data(){
            return {
                amount : '',
                errMsg : '',
            };
        },
        watch: {
            amount: function(){
                this.errMsg = this.lessThanMinBalance() ?  'Your wallet balance cannot be less than ₦'+Intl.NumberFormat().format(this.limit) : '';
            }
            
        },
        methods : {

            lessThanMinBalance(){
                return (this.wallet - this.amount) < this.limit
            },
            submit(e){
                return this.errMsg.length === 0 ? true : e.preventDefault();
            }
        },
        computed : {
            maxWithdrawal(){
                return Intl.NumberFormat().format(this.wallet - this.limit);
            }
        }
    }
</script>