<template>
    <span>
        <a class="btn btn-xs btn-warning" href="#" v-if="loan.status === 2" @click="beginTransfer">
            <small>Place on Transfer</small>
        </a>
        <a class="btn btn-xs btn-info" href="#" v-else-if="loan.status === 4">
            <small>On Transfer (₦{{loan.sale_amount}})</small> 
        </a>
        <a class="btn btn-xs btn-warning" href="#" v-else>
            <small>Transferred</small>
        </a>
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>Place this loan on Transfer
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div>
                    <p class="text-danger"><strong>{{error_message}}</strong></p>
                    <p>Loaned Amount: {{loan.amount}}</p>
                    <p>Current Value: ₦ {{currentValue}}</p>
                    
                    <div class="form-group">
                        <label for="interest_percentage">How much do you want to sell?</label>
                        <input type="number" id="amount" v-model="saleAmount" class="form-control">
                    </div>
                 
                </div>
            </div>

            <div slot="footer">
                <div>
                    <button class="btn btn-info" :disabled="loading" @click="placeOnTransfer">
                        <i :class="buttonClass"></i>
                        Place On Transfer
                    </button>
                </div>
            </div>
        </modal>
    </span>
</template>
<script>
    export default {
        props: ['loanFund', 'currentValue'],
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
                saleAmount: 0,
                loan: {}
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
                this.showModal = true;    
            },
            
            placeOnTransfer() {
                let shouldProceed = confirm(`Are you sure?`);
                if(shouldProceed) {
                    this.startLoading();
                    let request = {};
                    request.loan_id = this.loan.id;
                    request.amount = this.saleAmount;
                    axios.post(`/loans/transfer/place`, request).then(response => {
                        if (response.data.status === 1) {
                            this.loan.status = response.data.loan.status;
                            this.loan.sale_amount = response.data.loan.sale_amount;
                            alert("Loan successfully placed on transfer");
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