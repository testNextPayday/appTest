<template>
    <span>
        <a class="btn btn-xs btn-success" href="#" @click="makeBid">
            Update Offer
        </a>
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>Make a counter offer
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div>
                    <p class="text-danger"><strong>{{error_message}}</strong></p>
                    <p>Interest Percentage: {{bidPercentage}}%</p>
                    <p>Amount Needed: ₦ {{loanRequest.amount}}</p>
                    <p>Return Amount: ₦ {{loanRequest.amount + (loanRequest.amount * bidPercentage/100)}}</p>
                    <p>Gain: ₦ {{loanRequest.amount * bidPercentage/100}}</p>
                    
                    
                    <div class="form-group">
                        <label for="interest_percentage">Interest Percentage</label>
                        <input min="1" max="100" type="number" id="interest_percentage" v-model="bidPercentage" class="form-control">
                    </div>
                 
                </div>
            </div>

            <div slot="footer">
                <div>
                    <button class="btn btn-info" :disabled="loading" @click="updateBid">
                        <i :class="buttonClass"></i>
                        Update Bid
                    </button>
                </div>
            </div>
        </modal>
    </span>
</template>
<script>
    export default {
        props: ['bid', 'loanRequest'],
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
                bidPercentage: 0
            };
        },
        
        mounted() {
            this.initializeBidPercentage();  
        },
        
        methods: {
            initializeBidPercentage() {
                this.bidPercentage = this.bid.interest_percentage;
            },
            
            makeBid() {
                this.showModal = true;    
            },
            
            updateBid() {
                if(this.bidPercentage < 1 || this.bidPercentage > 100) {
                    this.setError("Bid Percentage must be between than 1 and 100");
                    return;
                }
                
                let shouldProceed = confirm(`If the user accepts this bid, it would be automatically withdrawn from your wallet. Do you wish to continue?`);
                if(shouldProceed) {
                    this.startLoading();
                    let request = {};
                    request.bid_id = this.bid.id;
                    request.interest_percentage = this.bidPercentage;
                    axios.post(`/bids/update`, request).then(response => {
                        if (response.data.status === 1) {
                            alert("Bid updated successfully");
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
        }
    };
</script>