<template>
    <div>
        <button type="button" v-if="!userHasFunded" class="btn btn-outline-primary bid-button" @click="startFunding">
            Fund Loan
        </button>
        
        <button type="button" v-else class="btn btn-outline-danger bid-button">
            Funded
        </button>
        
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>Fund this Loan
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div>
                    <p class="text-danger"><strong>{{error_message}}</strong></p>
                    <p>Amount Needed: ₦ {{loanRequest.amount * fundPercentage / 100}}</p>
                    
                    <div class="form-group">
                        <label for="interest_percentage">Fund Percentage</label>
                        <vue-slider
                            ref="slider"
                            v-model="fundPercentage"
                            v-bind="sliderOptions"
                          ></vue-slider>
                          
                          <h4 class="text-center">{{fundPercentage}} %</h4>
                    </div>
                 
                </div>
            </div>

            <div slot="footer">
                <div>
                    <button class="btn btn-info" :disabled="loading" @click="fundLoan">
                        <i :class="buttonClass"></i>
                        Fund Loan
                    </button>
                </div>
            </div>
        </modal>
    </div>
</template>
<script>
    import vueSlider from 'vue-slider-component';
    export default {
        props: ['loanRequest', 'funders', 'userId'],
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
                userHasFunded: false,
                fundPercentage: 3,
                sliderOptions: {
                    eventType: 'auto',
                    width: 'auto',
                    height: 6,
                    dotSize: 16,
                    dotHeight: null,
                    dotWidth: null,
                    min: 0,
                    max: 100,
                    interval: 1,
                    show: true,
                    speed: 0.5,
                    disabled: false,
                    piecewise: false,
                    piecewiseStyle: false,
                    piecewiseLabel: false,
                    tooltip: false,
                    tooltipDir: 'top',
                    reverse: false,
                    data: null,
                    clickable: true,
                    realTime: false,
                    lazy: false,
                    formatter: null,
                    bgStyle: null,
                    sliderStyle: null,
                    processStyle: null,
                    piecewiseActiveStyle: null,
                    piecewiseStyle: null,
                    tooltipStyle: null,
                    labelStyle: null,
                    labelActiveStyle: null
                }
            };
        },
        
        mounted() {
            this.confirmIfUserHasFunded();
        },
        
        methods: {
            confirmIfUserHasFunded() {
                 let user = this.funders.find((user) => user.id === this.userId);
                 if(user) {
                     //set bid percentage to user's percentage
                     this.userHasFunded = true;
                 }
            },
            
            startFunding() {
                this.showModal = true;    
            },
            
            fundLoan() {
                let shouldProceed = confirm(`₦ ${this.loanRequest.amount * this.fundPercentage / 100}, would be automatically withdrawn from your wallet. Do you wish to continue?`);
                if(shouldProceed) {
                    this.startLoading();
                    let request = {};
                    request.loan_request_id = this.loanRequest.id;
                    request.fund_percentage = this.fundPercentage;
                    axios.post(`/lenders/loan-requests/fund`, request).then(response => {
                        if (response.data.status === 1) {
                            this.userHasFunded = true;
                            alert("Loan funded successfully");
                            this.showModal = false;
                        } else {
                            this.setError(response.data.message);
                        }
                        this.stopLoading();
                    }).catch(error => {
                        this.setError(error.message);
                        this.stopLoading();
                    });
                }
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
                this.error_message = message;
            }
        },
        
        components: {
            'vue-slider': vueSlider
        }
    };
</script>