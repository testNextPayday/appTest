<template>
    <div>
        <div v-if="loading">
            <loader1></loader1>
        </div>

        <div v-else>

            <div >

                <h3>Check Paystack Balance</h3>
                <check-balance></check-balance>
                
            </div>

            <hr>

            <div id="otp-manager">

                <h3>Manage Transfer OTPs</h3>

                <div>

                    <h5>Enable OTP</h5>
                    <form method="POST" @submit.prevent="enableOtp">

                        <button type="submit" class="btn btn-primary btn-xs">Enable Otp</button>

                    </form>
                    
                </div>

                <div style="width:50%;">

                    <h5>Disable OTP</h5>

                    <form method="POST" @submit.prevent="disableOtp" v-if="!showOtpInput">

                        <button type="submit" class="btn btn-danger btn-xs">Disable Otp</button>
                        
                    </form>

                    <form method="POST" @submit.prevent="finalizeDisableOtp" v-if="showOtpInput">

                        <input type="text" v-model="otp" class="form-control">
                        <button type="submit" class="btn btn-warning btn-xs">Finalise Disable Otp</button>
                        
                    </form>

                </div>

            </div>

        </div>
    </div>
</template>
<script>

import { utilitiesMixin } from './../../mixins';

import checkBalance from './checkBalance';

export default {

    mixins: [utilitiesMixin],

    components : {
        'check-balance' : checkBalance
    },

    data(){

        return {

            otp : '',
            showOtpInput : false
        };
    },
    methods : {

        async enableOtp(){

            this.startLoading();

            await axios.post('/ucnull/transfer-controls/enable/otp').then((res)=> {

                this.alertSuccess(res.data);

            }).catch((e)=>{

                this.alertError(e.response.data)
            })

            this.stopLoading();
        },

        async disableOtp(){

            this.startLoading();

            await axios.post('/ucnull/transfer-controls/disable/otp').then((res)=> {

                this.showOtpInput = true;

                this.alertSuccess(res.data);

            }).catch((e)=>{

                this.alertError(e.response.data)
            })

            this.stopLoading();
        },

        async finalizeDisableOtp(){

            this.startLoading();

            const request = {otp : this.otp};

            await axios.post('/ucnull/transfer-controls/final/disable/otp',request).then((res)=>{

                this.alertSuccess(res.data);

            }).catch((e)=> {

                this.alertError(e.response.data);
            });

            this.showOtpInput  = false;

            this.stopLoading();
        },

       
    }
    
}
</script>