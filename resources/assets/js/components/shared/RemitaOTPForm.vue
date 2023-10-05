<template>

    <div>

        <br>
        <!-- If otp is enabled on the system -->
        <div v-if="otpenabled">
            
            <div v-if="remitabanks.includes(bankCode)">
            
                <form method="POST" @submit.prevent="validateOtp" v-if="canValidateOtp">


                    <!-- for zenith bank -->
                    <div v-if="bankCode == '057'">

                        <div class="form-group">
                            <label class="form-control-label">Last 4 card digits</label>
                            <input type="text" name="last4digits" v-model="card" class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">Enter OTP Sent to Phone</label>
                            <input type="text" name="otp" v-model="otp" class="form-control">
                        </div>

                    </div>


                    <div v-if="last6Digits.includes(bankCode)">

                        <div class="form-group">
                            <label class="form-control-label">Last 6 card digits</label>
                            <input type="text" name="last6digits" v-model="card" class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">Enter OTP Sent to Phone</label>
                            <input type="text" name="otp" v-model="otp" class="form-control">
                        </div>

                    </div>

                    <div v-if="onlyOtpBanks.includes(bankCode)">
                        
                        <div v-if="bankCode == '070'">
                    
                            <h4> <small><i>Dial *770*04# to get the OTP (Fidelity Bank Only)</i></small></h4>
                        
                            <div class="form-group">
                                <label class="form-control-label">Enter OTP</label>
                                <input type="text" name="otp" v-model="otp" class="form-control">
                            </div>
                            

                        </div>
                        
                        <div class="form-group" v-else>
                            <label class="form-control-label">Enter OTP Sent to Phone</label>
                            <input type="text" name="otp" v-model="otp" class="form-control">
                        </div>

                    </div>

                    

                    <newton-loader v-if="loading"></newton-loader>
                    <div class="form-control" style="border:none" v-else>
                        <button name="submit" class="btn btn-xs btn-success btn-rounded">Validate OTP <i :class="spinClass"></i></button>
                    </div>

                    

                </form>

                <form method="POST" v-else @submit.prevent="requestOtp">

                    <div class="form-control" style="border:none">
                        <button name="submit" class="btn btn-xs btn-danger btn-rounded">Request OTP <i :class="spinClass"></i></button>
                    </div>
                </form>

            </div>

            <div v-else>
                <h4 style="color:crimson"><small>User bank does not support otp with remita. Simply print the mandate information below</small></h4>
                <br>
                <a class="btn btn-link" target="_blank" :href="mandateurl">
                    * Print Mandate Here
                </a>
            </div>

        </div>
        

        <div v-else>
            <h4 style="color:crimson"><small>Simply print the mandate information below</small></h4>
            <br>
            <a class="btn btn-link" target="_blank" :href="mandateurl">
                * Print Mandate Here
            </a>
        </div>
    </div>

</template>
<script>
    import {utilitiesMixin} from './../../mixins';

    export default {

        props : ["bank","loan", "mandateurl", "otpenabled"],

        mixins : [utilitiesMixin],

        data(){

            return{

                onlyOtpBanks : ["214", "030", "076", "232", "100", "215",  "035", "301", "101", "070"],
                last6Digits : ["214","035"],
                otp : '',
                card : '',
                bankCode : this.bank.bank_code,
                canValidateOtp : false,
                spinClass : '',
                remitabanks : []
            };

        },

        mounted(){

            this.getRemitaBanks();

        },
        methods : {

            async validateOtp(){

                const request = {card : this.card, otp : this.otp }

                this.startSpining();

                await axios.post(`/ucnull/loans/mandate-validate-otp/${this.loan.reference}`,request).then((res)=>{

                    this.alertSuccess(res.data)

                    this.stopSpining();

                    this.$emit('refresh');

                }).catch((e)=>{

                    this.stopSpining();
                   
                    
                    this.alertError(e.response.data);
                })
            },


            async requestOtp(){

                this.startSpining();

                await axios.post(`/ucnull/loans/mandate-request-otp/${this.loan.reference}`).then((res)=>{

                     this.stopSpining();

                    this.canValidateOtp = true;

                    this.alertSuccess(res.data);
                  
                }).catch((e)=>{

                    this.stopSpining();

                    this.alertError(e.response.data);

                })

            },

            async getRemitaBanks()
            {
                await axios.get(`/ucnull/loans/remita/banks`).then((res)=>{

                    this.remitabanks = res.data;

                }).catch((e)=>{

                  this.alertError(e.response.message);

                })
            },

            startSpining(){

                this.spinClass = 'fa fa-spinner fa-spin';

            },

            stopSpining(){

                this.spinClass = '';

            }

        }
    }
</script>

<style scoped>

    .form-control {
        padding-left:0px;
       
    }

</style>