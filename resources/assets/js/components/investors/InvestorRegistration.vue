<template>
    <div>
        
        <div v-if="hasPendingData" class="alert alert-primary col-sm-8 my-5 mx-auto">
            Your application to become an investor is currently pending. 
            We will notify you as soon as there's a change in your application status.
        </div>
        
        <form v-else method="POST" @submit.prevent="submitApplication" enctype="multipart/form-data">
            <div class="form-group">
                <label>Enter Tax Number</label>
                
                <input type="text" class="form-control" v-model="formData.tax_number"
                        name="tax_number" placeholder="Enter Govt. Tax Identification Number" required />
            </div>
                        
            <fieldset>
                
                <div class="form-group">
                    <label class="label-file">
                        Attach a valid means of ID (JPG, PNG)
                        <span class="pull-right"><b>Max Size: 200 KB</b></span>
                    </label>
                    <input type="file" id="licenceImage" name="licence" ref="licenceImage"
                        @change="onFileChange('licenceImage')" accept="image/jpg,image/png" class="form-control" required>
                    <p class="text-danger"><b>{{imageErrors.licenceImage}}</b></p>
                    <p class="text-success"><b>{{imageSuccess.licenceImage}}</b></p>
                    
                </div>
            </fieldset>
            
            <fieldset v-if="isCompany == 1">
                <div class="form-group">
                    <label class="label-file">Attach Registration Certificate (JPG, PNG)
                        <span class="pull-right"><b>Max Size: 200 KB</b></span>
                    </label>
                    <input type="file" id="regCertificate" ref="regCertificate"
                        @change="onFileChange('regCertificate')" name="reg_certificate"
                        class="form-control" accept="image/jpg,image/png" required>
                    <p class="text-danger"><b>{{imageErrors.regCertificate}}</b></p>
                    <p class="text-success"><b>{{imageSuccess.regCertificate}}</b></p>
                </div>
            </fieldset>
            
            <div class="checkbox">
                <label for="agree">
                    <input type="checkbox" id="agreeTerms" v-model="agreeTerms" name="agreeTerms" required>&nbsp; I agree to the <a href="http://unicredit.ng/lterms-and-conditions/" target="_blank">Terms and Conditions</a> of Nextpayday.
                </label>
            </div>
                   
            <div v-if="agreeTerms">
                <div v-if="wallet < verificationFee">
                    <hr/>
                    <p><strong><i>
                       Nextpayday is required to conduct due diligence on our investors,
                               a verification fee of N {{verificationFee}} is charged for this service.
                               Please fund your wallet to proceed.
                    </i></strong></p>
                    <button type="button" class="btn btn-block btn-success" @click="goToGateway">
                        <i :class="spinClass"></i>
                        {{payButtonText}}
                    </button>
                    
                    <paystack ref="paystack" 
                        :pay-key="payKey" 
                        :email="email" 
                        :amount="verificationFee"
                        @paystack-response="paystackResponse"></paystack>
                    <hr/>
                </div>
               
                
                <button v-else type="submit" class="btn btn-sm btn-block btn-warning" :disabled="loading">
                    <i :class="spinClass"></i> Submit
                </button>
            </div>
        </form>
    </div>
</template>
<script>
    import Paystack from './../Paystack';
    import { utilitiesMixin } from './../../mixins';
    
    export default {
        mixins: [utilitiesMixin],
        
        props: ['verificationFee', 'hasPending', 'email', 'payKey', 'isCompany'],
        
        data() {
            return {
                formData: {
                    licence_type: 1,
                    licenceImage: '',
                    regCertificate: '',
                    tax_number: '',
                    managed_account: false,
                },
                imageErrors: {licenceImage: "", regCertificate: ""},
                imageSuccess: {licenceImage: "", regCertificate: ""},
                payButtonText: 'Fund Wallet',
                hasPendingData: false,
                agreeTerms: false
            };
        },
        
        mounted() {
            this.hasPendingData = this.hasPending;    
        },
        
        methods: {
            
            async submitApplication() {
                if (!this.formData.tax_number) return this.alertError("Please provide a tax number");
                
                if (!this.formData.licenceImage || this.formData.licenceImage.size > 200000)
                    return this.alertError("Please select a valid ID. Size limit: 200KB");
                    
                if (this.formData.lender_type == 2 && (!this.formData.regCertificate || this.formData.regCertificate.size > 200000))
                    return this.alertError("Please select a valid registration certificate. Size limit: 200KB");
                    
                let data = new FormData();
                Object.keys(this.formData).forEach(key => {
                    if (!(key == 'regCertificate' && this.formData.lender_type == 1))
                        data.append(key, this.formData[key]); 
                });
                
                this.startLoading();
                try {
                    const response = await axios.post('/investor/verification', data, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                        
                    if (response.data.status === 1) {
                        this.alertSuccess(response.data.message);
                        this.hasPendingData = true;
                    } else {
                        this.alertError(response.data.message);
                    }
                    this.stopLoading();
                } catch(error) {
                    this.handleApiErrors(error);                    
                    this.stopLoading();
                }
            },
            
            goToGateway() {
                this.startLoading();
                this.$refs.paystack.initiatePayment();
            },
            
            paystackResponse(event) {
                this.payButtonText = "Verifying Payment";
                let data = {
                    reference: event.reference, 
                    amount: this.verificationFee
                };
                
                axios.post('/payments/wallet-fund/verify', data).then(response => {
                    if(response.data.status === 1) {
                        this.alertSuccess(response.data.message);
                        this.$store.dispatch('updateWallet', data.amount);
                    } else {
                        this.alertError(response.data.message);
                    }    
                    this.stopLoading();
                }).catch(error => {
                    this.alertError('An error occurred. Please try again');
                    this.stopLoading();
                });
            },
            
            onFileChange(refName) {
                this.formData[refName] = this.$refs[refName].files[0];
                //check file size here
                if (this.formData[refName].size > 200000) {
                    this.imageErrors[refName] = "Please select an image less than 200 KB";   
                    this.imageSuccess[refName] = "";
                } else {
                    this.imageErrors[refName] = "";
                    this.imageSuccess[refName] = "File OK";
                }
            },

        },
        
        computed: {
            wallet() {
                return this.$store.getters.getWallet;
            }
        },
        
        components: {
            'paystack': Paystack
        }
    };
</script>
<style scoped>
    form {
        width: 100% !important;
    }
    
    legend {
        font-size: 80%;
    }
    
    label {
        font-weight: bold;
    }
    
    .label-file {
        width: 100%;
    }
    
    .pull-right {
        display: inline-block;
        float: right;
    }
</style>