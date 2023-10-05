<template>
    <div>
        <investment-status :status="fund.status"></investment-status>                
        <p>
            Fund Action: &nbsp;
                <a v-if="fund.status == 2" href="javascript:;"
                    class="btn btn-primary" @click="beginTransfer" style="font-size:13px;">Transfer Investment</a>
                <span v-else class="badge badge-secondary">No action available</span>
        </p>
        
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>Place this item on the market
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div>
                    <p>Initial Amount: {{fund.amount}}</p>
                    <p>Current Value: ₦ {{currentValue}}</p>
                    
                    <div class="form-group">
                        <label for="interest_percentage">How much do you want to sell for?</label>
                        <input type="number" id="amount" v-model="saleAmount" class="form-control">
                    </div>
                </div>
            </div>

            <div slot="footer">
                <div>
                    <button class="btn btn-info" :disabled="loading" @click="placeOnTheMarket">
                        <i :class="spinClass"></i>
                        Place on the Market
                    </button>
                </div>
            </div>
        </modal>
    </div>
</template>
<script>
import { utilitiesMixin } from './../../mixins';
import InvestmentStatus from './InvestmentStatus';

export default {
    props: ['fund', 'currentValue', 'repayment'],
    
    mixins: [utilitiesMixin],
    
    data() {
        return {
            showModal: false,
            saleAmount: '',
        };
    },
    
    mounted() {
        this.initialize();  
    },
    
    methods: {
        initialize() {
            this.loan = this.loanFund;
            this.saleAmount = this.currentValue;
        },
        
        beginTransfer() {
            if (this.repayment === undefined || this.repayment.length == 0) {
                    // array empty or does not exist
                    this.showModal = true; 
             }else{
                let item  = this.repayment.slice(-1)[0];
                let date  = new Date(item.created_at);
                let toDay = new Date();
                toDay.setMonth(toDay.getMonth() - 3); //Add 3 months to current month
                if (date > toDay) {
                     this.showModal = true; 
                }else{
                    // console.log("greater than 3 months ");
                    this.alertError("Error! Bad repayment");
                }
             }
        },  
        
        async placeOnTheMarket() {
            let shouldProceed = confirm(`Are you sure?`);
            
            if (!shouldProceed) return;
            
            this.startLoading();
            
            const request = {   amount: this.saleAmount };
            
            try {
                const response = await axios.post(`/funds/${this.fund.reference}/transfer`, request);
                console.log(response);
                if (response.data.status === 1) {
                    this.fund.status = response.data.fund.status;
                    this.fund.sale_amount = response.data.fund.sale_amount;
                    this.alertSuccess(response.data.message);
                    this.showModal = false;
                
                } else {
                    this.alertError(response.data.message);
                }
                
                this.stopLoading();
            } catch (error) {
                this.handleApiErrors(error);
                this.stopLoading();
            }
                
        },
    },
    
    components: {
        'investment-status': InvestmentStatus,
    },
};
</script>