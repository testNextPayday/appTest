<template>
    <span>
        <button class="btn btn-outline-danger" type="button" v-if="!isVerified" @click="startVerification">Verify</button>
        <button class="btn btn-outline-success" type="button" v-else>Verified</button>
        
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>Verify your phone number
                    <span class="justify-content-right"  v-if="!loading">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div>
                    <p class="text-danger text-center">{{error_message}}</p>
                    <p class="text-primary text-center">{{success_message}}</p>
                    <div class="form-group" v-if="page === 1">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" v-model="phoneNumber" class="form-control" readonly>
                    </div>
                    <div class="form-group" v-else>
                        <label for="code">Verification Code</label>
                        <input type="text" id="code" v-model="code" class="form-control" placeholder="Enter verification code">
                    </div>
                </div>
            </div>

            <div slot="footer">
                <div>
                    <button class="btn btn-info" :disabled="loading" @click="requestVerificationCode" v-if="page === 1">
                        <i :class="buttonClass"></i>
                        Get Verification Code
                    </button>
                    
                    <button class="btn btn-info" :disabled="loading" @click="verfiyNumber" v-else>
                        <i :class="buttonClass"></i>
                        Verify Phone Number
                    </button>
                </div>
            </div>
        </modal>
    </span>
</template>
<script>
    export default {
        props: ['phoneVerified', 'phone'],
        data() {
            return {
                showModal: false,
                loading: false,
                buttonClass: {
                    fa: true,
                    "fa-check-circle-o": true,
                    "fa-spin": false,
                    "fa-spinner": false
                },
                error_message: '',
                success_message: '',
                phoneNumber: 0,
                isVerified: false,
                page: 1,
                code: null
            };
        },
        
        mounted() {
            this.init();
        },
        
        methods: {
            init() {
                 this.phoneNumber = this.phone;
                 this.isVerified = this.phoneVerified;
            },
            
            startVerification() {
                this.showModal = true;    
            },
            
            requestVerificationCode() {
                if(!this.phoneNumber) {
                    this.setError("Please provide a phone number");
                    return;
                }
                
                this.startLoading();
                let request = {};
                request.phone = this.phoneNumber;
                axios.post(`/profile/phone-verification/code`, request).then(response => {
                    if (response.data.status === 1) {
                        this.page = 2;
                        this.setSuccess("Please enter the code that was sent to your phone");
                    } else {
                        this.setError(response.data.message);
                    }
                    this.stopLoading();
                }).catch(error => {
                    this.setError(error.message);
                    this.stopLoading();
                });
            },
            
            verfiyNumber() {
                if(!this.code) {
                    this.setError("Please enter your code");
                    return;
                }
                
                this.startLoading();
                let request = {};
                request.code = this.code;
                axios.post(`/profile/phone-verification/verify`, request).then(response => {
                    if (response.data.status === 1) {
                        this.setSuccess("Verification successful. Please wait...");
                        window.location = window.location;
                    } else {
                        this.setError(response.data.message);
                    }
                    this.stopLoading();
                }).catch(error => {
                    this.setError(error.message);
                    this.stopLoading();
                });
            },
            
            startLoading() {
                this.loading = true;
                this.buttonClass['fa-spinner'] = true;
                this.buttonClass['fa-spin'] = true;
                this.buttonClass['fa-check-circle-o'] = false;
            },
            
            stopLoading() {
                this.loading = false;
                this.buttonClass['fa-spinner'] = false;
                this.buttonClass['fa-spin'] = false;
                this.buttonClass['fa-check-circle-o'] = true;
            },
            
            setError(message) {
                this.success_message = '';
                this.error_message = message;
            },
            
            setSuccess(message) {
                this.error_message = '';
                this.success_message = message;
            }
        }
    };
</script>
<style scoped>
    .input-group .form-control {
        width: 100%;
    }
</style>