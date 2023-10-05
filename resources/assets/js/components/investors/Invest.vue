<template>
    <span>
        <button class="btn btn-info btn-xs mt-3 mb-4"
            v-if="!invested" @click="startInvesting">Invest</button>
        
        <button v-else class="btn btn-danger btn-xs mt-3 mb-4" style="cursor:not-allowed" disabled>
            Invested
        </button>
        
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>Make Investment
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div>
                    <p class="text-danger"><strong>{{error_message}}</strong></p>
                    <p>Amount Needed: ₦ {{loanRequest.amount * percentage / 100}}</p>
                    
                    <div class="form-group" v-if="loanRequest.investor_id != investorId">
                        <label for="interest_percentage">Investment Percentage</label>
                        <vue-slider
                            ref="slider"
                            v-model="percentage"
                            v-bind="sliderOptions"
                          ></vue-slider>
                          
                          <h4 class="text-center">{{percentage}} %</h4>
                    </div>
                 
                </div>
            </div>

            <div slot="footer">
                <div>
                    <button class="btn btn-info" :disabled="loading" @click="invest">
                        <i :class="spinClass"></i>
                        Plac{{ loading ? 'ing' : 'e' }} Investment
                    </button>
                </div>
            </div>
        </modal>
    </span>
</template>
<script>
    import vueSlider from 'vue-slider-component';
    
    import { utilitiesMixin } from './../../mixins';
    
    export default {
        props: ['loanRequest', 'investors', 'investorId'],
        
        mixins: [utilitiesMixin],
        
        data() {
            return {
                showModal: false,
                invested: false,
                percentage: 3,
                error_message: '',
            };
        },
        
        mounted() {
            this.checkUserInvestment();
            if (this.investorId == this.loanRequest.investor_id) this.percentage = 100;
        },
        
        methods: {
            checkUserInvestment() {
                 let investor = this.investors.find((investor) => investor.id === this.investorId);
                 if(investor) {
                     //set bid percentage to user's percentage
                     this.invested = true;
                 }
            },
            
            startInvesting() {
                this.showModal = true;    
            },
            
            async invest() {
                if(this.percentage < 1) {
                    this.alertError("Percentage must be greater than 1");
                    return;
                }
                
                const proceed = confirm(`₦ ${this.loanRequest.amount * this.percentage / 100}, would be automatically withdrawn from your wallet. Do you wish to continue?`);
                if(!proceed) return;
                
                this.startLoading();
                const data = {
                    percentage: this.percentage
                };
                
                try {
                    const response = await axios.post(`/requests/${this.loanRequest.reference}/fund`, data);
                            
                    if (response.data.status === 1) {
                        this.invested = true;
                        this.alertSuccess("Investment made successfully");
                        this.showModal = false;
                    } else {
                        this.alertError(response.data.message);
                        this.error_message = response.data.message;
                    }
                    
                    this.stopLoading();
                } catch(e) {
                    this.handleApiErrors(e);
                    this.stopLoading();
                }
            },
            
        },
        
        components: {
            'vue-slider': vueSlider
        }
    };
</script>