<template>
    <div>
        <button type="button" v-if="!userHasBidded" class="btn btn-outline-primary bid-button" @click="makeBid">
            Place Bid
        </button>
        
        <button type="button" v-else class="btn btn-outline-danger bid-button">
            Bid Placed
        </button>
        
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>Place Your Bid
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div>
                    <p class="text-danger"><strong>{{error_message}}</strong></p>
                    <h4>Owner's Offer: ₦ {{loan.sale_amount}}</h4>
                    <p>Loan Value: ₦ {{loanValue}}</p>
                    <p>Gain: ₦ {{loanValue - bidAmount}}</p>
                    <div class="checkbox">
                        <label for="checkbox1">
                            <input type="checkbox" id="checkbox1" v-model="biddingDifferently"> Offer a different amount
                        </label>
                    </div>
                    
                    <div class="form-group" v-if="biddingDifferently">
                        <label for="bidAmount">Interest Percentage</label>
                        <input type="number" id="bidAmount" v-model="bidAmount" class="form-control">
                    </div>
                 
                </div>
            </div>

            <div slot="footer">
                <div>
                    <button class="btn btn-info" :disabled="loading" @click="placeBid">
                        <i :class="buttonClass"></i>
                        Place Bid
                    </button>
                </div>
            </div>
        </modal>
    </div>
</template>
<script>
    export default {
        props: ['loan', 'loanValue', 'bidders', 'userId'],
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
                userHasBidded: false,
                biddingDifferently: false,
                bidAmount: 1000
            };
        },
        
        mounted() {
            this.confirmIfUserHasBidded();
        },
        
        methods: {
            confirmIfUserHasBidded() {
                 let user = this.bidders.find((user) => user.id === this.userId);
                 if(user) {
                     this.userHasBidded = true;
                 } else {
                      this.setInitalBidAmount();
                 }
            },
            
            setInitalBidAmount() {
                this.bidAmount = this.loan.sale_amount;
            },
            
            makeBid() {
                this.showModal = true;    
            },
            
            placeBid() {
                if(!this.bidAmount) {
                    this.setError("Please enter a valid bid amount");
                    return;
                }
                
                let shouldProceed = confirm(`If the user accepts this bid, it would be automatically withdrawn from your wallet. Do you wish to continue?`);
                if(shouldProceed) {
                    this.startLoading();
                    let request = {};
                    request.loan_id = this.loan.id;
                    request.amount = this.bidAmount;
                    axios.post(`/lenders/bids/place`, request).then(response => {
                        if (response.data.status === 1) {
                            this.userHasBidded = true;
                            alert("Bid placed successfully");
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
        
        watch: {
            "biddingDifferently": function(current) {
                if(!current) {
                    this.setInitalBidAmount();
                }
            }
        }
    };
</script>