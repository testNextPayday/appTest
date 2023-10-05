<template>

    <div>
        <div v-if="transaction.status_message == 'otp'">

            <div v-if="isFinalizingTransaction">
                <newton-loader></newton-loader>
            </div>

            <div v-else>
                <input type="text" v-model="otp">

                <a href="#" class="badge badge-warning" @click="finalizeTransaction">Finalize with OTP</a>

                <a href="#" class="badge badge-primary" @click="resendOtp">Resend OTP</a>
            </div>
            

        </div>

        <div v-else-if="transaction.status_message == 'pending'">

            <div v-if="isVerifyingTransaction">
                <newton-loader></newton-loader>
            </div>
            
                <a href="#" class="badge badge-warning" @click="retryTransaction" v-else>Verify Status</a>

        </div>

        <div v-else-if="transaction.status_message == 'success'">

                <a href="#" class="badge badge-success">Success - No Action</a>

        </div>

            <div v-else-if="transaction.status_message == 'failed'">

                <div v-if="isCreatingTransaction">
                    <newton-loader></newton-loader>
                </div>
            
                <a href="#" class="btn btn-xs btn-warning" @click="newTransaction" v-else>Spawn New Transaction</a>

        </div>

        <div v-else>

            <a href="#" class="badge badge-primary">Old Transaction</a>

        </div>

    </div>

</template>

<script>

import { utilitiesMixin } from './../../mixins';

export default {

    mixins: [utilitiesMixin],

    props : {

        proptransaction : {

            type : Object,
            required : true
        } 
    },

    data(){

        return {

            transaction : this.proptransaction,

            otp : '',

            isVerifyingTransaction : false,

            isFinalizingTransaction : false,

            isCreatingTransaction : false
        };
    },

    computed : {

        status(){

            return this.transaction.pay_status
        }
    },

    methods : {

         async retryTransaction(){

            this.isVerifyingTransaction = true;

            await axios.post(`/ucnull/gateway-transactions/retry/${this.transaction.id}`).then((res)=>{

                this.alertSuccess(res.data);
                this.refreshTransaction();

            }).catch((e)=>{

                this.alertError(e.response.data);
            })

            this.isVerifyingTransaction = false;

        },

         async newTransaction(){

            this.isCreatingTransaction = true;

            await axios.post(`/ucnull/gateway-transactions/new/${this.transaction.id}`).then((res)=>{

                this.alertSuccess(res.data);
                this.refreshTransaction();

            }).catch((e)=>{

                this.alertError(e.response.data);
            })

            this.isCreatingTransaction = false;

        },

        async resendOtp(){

            this.isVerifyingTransaction = true;

            await axios.post(`/ucnull/gateway-transactions/resend-otp/${this.transaction.id}`).then((res)=>{

                this.alertSuccess(res.data);
                this.refreshTransaction();

            }).catch((e)=>{

               this.alertError(e.response.data);
            })

            this.isVerifyingTransaction = false;

        },
        async  finalizeTransaction(){

            if(! this.otp){

                this.alertError(' Enter otp ');

                return false;
            }

            this.isFinalizingTransaction = true;

            const request = {otp : this.otp, transfer_code : this.transaction.transaction_id};

            await axios.post(`/ucnull/gateway-transactions/finalize/${this.transaction.id}`,request).then((res)=>{

                this.alertSuccess(res.data);
                this.refreshTransaction();

            }).catch((e)=>{
                
                this.alertError(e.response.data);
            })

             this.isFinalizingTransaction = false;
        },

        async refreshTransaction(){

            await axios.get(`/ucnull/gateway-transactions/get/${this.transaction.id}`).then((res)=>{

                this.transaction = res.data;

            }).catch((e)=> {

               this.alertError(e.response.data);

            });
        }
    }
}
</script>