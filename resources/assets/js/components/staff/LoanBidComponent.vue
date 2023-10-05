<template>
    <div>
        <button type="button" v-if="!userHasBidded" class="btn btn-outline-primary bid-button" @click="makeBid">
            Place Bid
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
                    <p>Gain: ₦ {{loan.potential_gain}}</p>
                    <div class="checkbox">
                        <label for="checkbox1">
                            <input type="checkbox" id="checkbox1" v-model="biddingDifferently"> Offer a different amount
                        </label>
                    </div>
                    
                    <div class="form-group" v-if="biddingDifferently">
                        <label for="bidAmount">Interest Percentage</label>
                        <input type="number" id="bidAmount" v-model="bidAmount" class="form-control" @input="setUpValidAccounts">
                    </div>
                    
                    <div v-if="accounts.length > 0">
                        <div class="form-group" v-if="validAccounts.length > 0">
                            <label for="user">Select Account</label>
                            <select v-model="bidder" class="form-control">
                                <option v-for="validAccount in validAccounts" :value="validAccount.id">
                                    {{validAccount.name}} - {{validAccount.wallet}}
                                </option>
                            </select>
                        </div>
                        <p v-else>* None of your accounts have enough money to place this</p>
                    </div>
                    <p v-else>* You have no managed account to make this bid with.</p>
                 
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
        props: ['loan', 'loanValue', 'bidders', 'accounts'],
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
                biddingDifferently: false,
                bidAmount: 1000,
                accountsThatHaveNotBidded: [],
                biddersCopy: [],
                validAccounts: [],
                bidder: 0,
            };
        },
        
        mounted() {
            this.setUp();
        },
        
        methods: {
            setUp() {
                this.biddersCopy = this.bidders;
                
                this.accounts.forEach((account) => {
                    if(account.id != this.loan.investor_id) {
                        let user = this.biddersCopy.find((user) => user.id === account.id);
                        if(!user) {
                            this.accountsThatHaveNotBidded.push(account);
                        }    
                    }
                });
                this.setInitalBidAmount();
                this.setUpValidAccounts();
            },
            
            setUpValidAccounts() {
                this.validAccounts = [];
                this.bidder = 0;
                this.accountsThatHaveNotBidded.forEach((account) => {
                    if(account.wallet >= this.bidAmount) {
                        this.validAccounts.push(account);
                    } 
                });
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
                
                if(this.bidder == 0) {
                    this.setError("You need a valid account");
                    return;
                }
                
                let shouldProceed = confirm(`If the user accepts this bid, it would be automatically withdrawn from this account. Do you wish to continue?`);
                if(shouldProceed) {
                    this.startLoading();
                    let request = {};
                    request.loan_id = this.loan.id;
                    request.amount = this.bidAmount;
                    request.investor_id = this.bidder;
                    axios.post(`/staff/bids/loans/place`, request).then(response => {
                        if (response.data.status === 1) {
                            let user = this.validAccounts.find((account) => account.id === this.bidder);
                            let index = this.validAccounts.indexOf(user);
                            if(index != -1) {
                                this.validAccounts.splice(index, 1);
                            }
                            alert("Bid placed successfully");
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