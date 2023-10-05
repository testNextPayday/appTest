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
                <div style="text-align:left;">
                    <p class="text-danger"><strong>{{error_message}}</strong></p>
                    <h4>Owner's Offer: ₦ {{loan.sale_amount}}</h4>
                    <p>Loan Value: ₦ {{ loanValue }}</p>
                    <p>Return Date: {{ loan.due_date }}</p>
                    <p>Gain: ₦ {{ loanValue - bidAmount}}</p>
                    
                    
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input min="1" max="100" type="number" id="amount" v-model="bidAmount" class="form-control">
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
        props: ['bid', 'loan', 'loanValue', 'url'],
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
                bidAmount: 1000
            };
        },
        
        mounted() {
            this.initializeBidAmount();  
        },
        
        methods: {
            initializeBidAmount() {
                this.bidAmount = this.bid.amount;
            },
            
            makeBid() {
                this.showModal = true;    
            },
            
            updateBid() {
                if(!this.bidAmount) {
                    this.setError("Please enter a valid bid amount");
                    return;
                }
                
                let shouldProceed = confirm(`If the user accepts this bid, ₦${this.bidAmount} would automatically withdrawn. Do you wish to continue?`);
                if(shouldProceed) {
                    this.startLoading();
                    let request = {};
                    request.bid_id = this.bid.id;
                    request.amount = this.bidAmount;
                    axios.post(this.url, request).then(response => {
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