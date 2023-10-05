<template>
    <div class="card">
        <div class="card-header">
            <strong>Lender</strong>
            <small>Activation</small>
            
            <span class="pull-right">
                Wallet Balance: NGN {{wallet}}
            </span>
        </div>
        
        <h5 class="text-danger text-center">
             {{errorMessage}}   
        </h5>
        
        <ul v-if="errorBag.length > 0">
            <li v-for="error in errorBag" class="text-danger">{{error}}</li>
        </ul>
        
        <div class="card-body" v-if="hasPendingData">
            Your application to become an investor is currently pending. 
            We will notify you as soon as there's a change in your application status.
        </div>
        <div v-else>
            <form method="POST" @submit.prevent="submitLenderForm" enctype="multipart/form-data">
                    
                <div class="card-body">
                    <div class="form-group">
                        <label for="company">Select Account Type</label>
                        <select name="lender_type" v-model.number="formData.lender_type" class="form-control" required>
                            <option value="1">Single User</option>
                            <option value="2">Institution/Company</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Licence Type</label>
                        <div class="radio">
                            <label for="radio1">
                                <input type="radio" id="radio1" v-model.number="formData.licence_type" name="licence_type" value="1" checked> CBN Licence
                            </label>
                        </div>
                        <div class="radio">
                            <label for="radio2">
                                <input type="radio" id="radio2" v-model.number="formData.licence_type" name="licence_type" value="2"> State Issued Licence
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-control">
                        <p><label>Upload Licence (JPG, PNG)</label> <span class="pull-right"><b>Max Size: 200 KB</b></span></p>
                        <p class="text-danger"><b>{{imageErrors.licenceImage}}</b></p>
                        <p class="text-success"><b>{{imageSuccess.licenceImage}}</b></p>
                        <input type="file" id="licenceImage" name="licence" ref="licenceImage" @change="onFileChange('licenceImage')" accept="image/jpg,image/png" class="form-control" required>
                    </div>
                    
                    <br v-if="formData.lender_type == 2"/>
                    <div class="form-control" v-if="formData.lender_type == 2">
                        <p><label>Upload Registration Certificate (JPG, PNG)</label> <span class="pull-right"><b>Max Size: 200 KB</b></span></p>
                        <p class="text-danger"><b>{{imageErrors.regCertificate}}</b></p>
                        <p class="text-success"><b>{{imageSuccess.regCertificate}}</b></p>
                        <input type="file" id="regCertificate" ref="regCertificate" @change="onFileChange('regCertificate')" name="reg_certificate" class="form-control" accept="image/jpg,image/png" required>
                    </div>
                  
                    <hr>
                  
                    <div class="form-group">
                        <label>Tax Number </label>
                        <input type="text" class="form-control" v-model="formData.tax_number"  name="tax_number" placeholder="Enter Govt. Tax Identification Number" required>
                    </div>
                       
                    <!--<p>-->
                    <!--    <b>Note:</b> Lender activation verification fee is {{verificationFee}}.-->
                    <!--</p> -->
                    
                    <div class="checkbox">
                        <label for="agree">
                            <input type="checkbox" id="agreeTerms" v-model="agreeTerms" name="agreeTerms" required> I agree to the <a href="http://unicredit.ng/lterms-and-conditions/" target="_blank">Terms and Conditions</a> of Nextpayday.
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
                            <button class="btn btn-block btn-success" @click="goToGateway">
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
                </div>
            </form>
        </div>
    </div>
</template>
<script>
    import Paystack from './Paystack';
    import { utilitiesMixin } from './../mixins';
    
    export default {
        mixins: [utilitiesMixin],
        
        props: ['verificationFee', 'hasPending', 'email', 'payKey'],
        
        data() {
            return {
                formData: {
                    lender_type: 1,
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
                page: 1,
                agreeTerms: false
            };
        },
        
        mounted() {
            this.hasPendingData = this.hasPending;    
        },
        
        methods: {
            
            submitLenderForm() {
                if (!this.formData.tax_number) return this.alertError("Please provide a tax number");
                
                if (!this.formData.licenceImage || this.formData.licenceImage.size > 200000)
                    return this.alertError("Please select a valid licence image. Size limit: 200KB");
                    
                if (this.formData.lender_type == 2 && (!this.formData.regCertificate || this.formData.regCertificate.size > 200000))
                    return this.alertError("Please select a valid registration certificate. Size limit: 200KB");
                    
                let data = new FormData();
                Object.keys(this.formData).forEach(key => {
                    if (!(key == 'regCertificate' && this.formData.lender_type == 1))
                        data.append(key, this.formData[key]); 
                });
                
                this.startLoading();
                axios.post('/profile/update/lender/activation', data, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(response => {
                    if (response.data.status === 1) {
                        this.alertSuccess(response.data.message);
                        this.hasPendingData = true;
                    } else {
                        this.alertError(response.data.message);
                    }
                    this.stopLoading();
                }).catch(error => {
                    if(error.response && error.response.status === 422) {
                        
                        this.errorMessage = error.response.data.message;
                        let errorMessages = Object.keys(error.response.data.errors);
                        errorMessages.forEach((errorKey) => {
                            this.errorBag.push(error.response.data.errors[errorKey][0]);
                        });
                        
                    } else {
                        this.errorMessage = error.message;
                        this.alertError(error.message);
                    }
                    
                    this.stopLoading();
                });
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