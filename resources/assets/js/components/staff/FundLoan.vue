<template>
    <div>
        <button type="button" class="btn btn-outline-primary bid-button" @click="startFunding">
            Fund Loan
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
                        <hr/>
                        <vue-slider
                            ref="slider"
                            v-model="fundPercentage"
                            v-bind="sliderOptions"
                            @change="setUpValidAccounts">
                        </vue-slider>
                        <hr/>
                        <h4 class="text-center">{{fundPercentage}} %</h4>
                    </div>
                    <div v-if="accounts.length > 0">
                        <div class="form-group" v-if="validAccounts.length > 0">
                            <label for="user">Select Account</label>
                            <select v-model="funder" class="form-control">
                                <option v-for="validAccount in validAccounts" :value="validAccount.id">
                                    {{validAccount.name}} - {{validAccount.wallet}}
                                </option>
                            </select>
                        </div>
                        <p v-else>* None of your accounts have enough money to fund this loan</p>
                    </div>
                    <p v-else>* You have no managed account to fund this loan with.</p>
                 
                </div>
            </div>

            <div slot="footer">
                <div>
                    <button class="btn btn-info" :disabled="loading || accounts.length < 1 || validAccounts.length < 1" @click="fundLoan">
                        <i :class="spinClass"></i>
                        Fund Loan
                    </button>
                </div>
            </div>
        </modal>
    </div>
</template>
<script>
    import vueSlider from 'vue-slider-component';
    import { utilitiesMixin } from './../../mixins';
    
    export default {
        props: ['loanRequest', 'funders', 'accounts'],
        
        mixins: [utilitiesMixin],
        
        data() {
            return {
                showModal: false,
                error_message: '',
                fundPercentage: 3,
                funder: 0,
                accountsThatHaveNotBidded:[],
                validAccounts:[],
                fundersCopy: {},
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
            this.setUp();
        },
        
        methods: {
            setUp() {
                this.fundersCopy = this.funders;
                this.accounts.forEach((account) => {
                    if(account.id != this.loanRequest.user_id) {
                        let user = this.fundersCopy.find((user) => user.id === account.id);
                        if(!user) {
                            this.accountsThatHaveNotBidded.push(account);
                        }    
                    }
                });
                
                this.setUpValidAccounts();
            },
            
            setUpValidAccounts() {
                this.validAccounts = [];
                this.funder = 0;
                let amountNeeded = this.loanRequest.amount * this.fundPercentage / 100;
                this.accountsThatHaveNotBidded.forEach((account) => {
                    if(account.wallet >= amountNeeded) {
                        this.validAccounts.push(account);
                    } 
                });
            },
            
            startFunding() {
                this.showModal = true;    
            },
            
            fundLoan() {
                if(this.funder == 0) {
                    alert('Please select an account');
                    return;
                }
                
                let shouldProceed = confirm(`₦ ${this.loanRequest.amount * this.fundPercentage / 100}, would be automatically withdrawn from your wallet. Do you wish to continue?`);
                if(shouldProceed) {
                    this.startLoading();
                    let request = {};
                    request.loan_request_id = this.loanRequest.id;
                    request.fund_percentage = this.fundPercentage;
                    request.investor_id = this.funder;
                    axios.post(`/staff/loan-requests/funds/place`, request).then(response => {
                        if (response.data.status === 1) {
                            let user = this.validAccounts.find((account) => account.id === this.funder);
                            let index = this.validAccounts.indexOf(user);
                            if(index != -1) {
                                this.validAccounts.splice(index, 1);
                            }
                            alert("Loan request funded successfully");
                        } else {
                            this.setError(response.data.message);
                        }
                        this.stopLoading();
                    }).catch(error => {
                        this.handleApiErrors(error);
                        this.stopLoading();
                    });
                }
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