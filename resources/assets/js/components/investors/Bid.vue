<template>
    <span>
        <button class="btn btn-info btn-xs mt-3 mb-4"
            v-if="!bidded" @click="startBidding">Make Bid</button>
        
        <button v-else class="btn btn-danger btn-xs mt-3 mb-4" style="cursor:not-allowed" disabled>
            Placed Bid
        </button>
        
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>Place Bid
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div style="text-align:left">
                    <h5>Price: ₦ {{fund.sale_amount}}</h5>
                    <hr/>
                    <h5>Asset Value: ₦ {{currentValue}}</h5>
                    <hr/>
                    <h5>Unearned Interest: ₦ {{potentialGain}}</h5>
                    <hr/>
                    <div class="checkbox">
                        <label for="checkbox1">
                             Offer a different amount  <input type="checkbox" id="checkbox1" v-model="biddingDifferently">
                        </label>
                    </div>
                    
                    <div class="form-group" v-if="biddingDifferently">
                        <label for="bidAmount">Alternative Amount</label>
                        <input type="number" id="bidAmount" v-model="bidAmount" class="form-control">
                    </div>
                 
                </div>
            </div>

            <div slot="footer">
                <div>
                    <button class="btn btn-info" :disabled="loading" @click="bid">
                        <i :class="spinClass"></i>
                        Place Bid
                    </button>
                </div>
            </div>
        </modal>
    </span>
</template>
<script>
    import { utilitiesMixin } from './../../mixins';
    
    export default {
        props: ['fund', 'currentValue', 'bidders', 'investorId','potentialGain'],
        
        mixins: [utilitiesMixin],
        
        data() {
            return {
                showModal: false,
                bidded: false,
                bidAmount: 1000,
                biddingDifferently: false,
            };
        },
        
        mounted() {
            this.checkInvestorBid();
        },
        
        methods: {
            checkInvestorBid() {
                let investor = this.bidders.find((investor) => investor.id === this.investorId);
                if(investor) {
                    this.bidded = true;
                } else {
                    this.setInitalBidAmount();
                }
            },
            
            setInitalBidAmount() {
                this.bidAmount = this.fund.sale_amount;
            },
            
            startBidding() {
                this.showModal = true;    
            },
            
            async bid() {
                if(!this.bidAmount) {
                    this.alertError("Please enter a valid bid amount");
                    return;
                }
                
                const proceed = confirm(`If the user accepts this bid, it would be automatically withdrawn from your wallet. Do you wish to continue?`);
                if(!proceed) return;
                
                this.startLoading();
                const data = {
                    amount: this.bidAmount
                };
                
                try {
                    const response = await axios.post(`/funds/${this.fund.reference}/market/bid`, data);
                            
                    if (response.data.status === 1) {
                        this.bidded = true;
                        this.alertSuccess("Bid placed successfully");
                        this.showModal = false;
                    } else {
                        this.alertError(response.data.message);
                    }
                    
                    this.stopLoading();
                } catch(e) {
                    this.handleApiErrors(e);
                    this.stopLoading();
                }
            },
            
        }
    };
</script>