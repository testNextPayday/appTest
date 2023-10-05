<template>
    <div>
        <div class="alert alert-info">
            <h4>
                <small class="text-uppercase">Monthly Repayment:</small> 
                <span style="float:right"><strong>₦ {{emi}}</strong></span></h4>
        </div>
        <div class="form-group">
            <label for="employer">Select Employer</label>
            <select class="form-control" id="employer" name="" v-model="employer_id" required>
                <option v-for="employer in employers" :value="employer.id">{{employer.name}}</option>
            </select>
            <br/>
        </div>
        
        <div v-if="employer && (employer.collection_plan || employer.collection_plan_secondary)">
            <h6 class="text-uppercase">Employer Collection Methods</h6>
            
            <p>
                <span class="badge badge-primary" v-if="parsedCollectionMethods[employer.collection_plan]">
                    {{ parsedCollectionMethods[employer.collection_plan] }}
                </span>
                
                <span class="badge badge-info m-r-2" v-if="parsedCollectionMethods[employer.collection_plan_secondary]">
                    {{ parsedCollectionMethods[employer.collection_plan_secondary] }}
                </span>
            </p>
            
            <br/>
            
        </div>
        
        <div class="form-group">
            <label for="company" style="display:block">Net Salary</label>
            <input type="number" class="form-control" id="amount" v-model="net_pay" name="net_pay" required
                placeholder="Enter your net salary">
        </div>
        
        <div class="form-group">
            <label for="duration" style="display:block">Loan Duration (Months) <span class="pull-right">Max: <strong>{{max_duration}}</strong></span></label>
            <input type="number" class="form-control" name="duration" v-model="duration" placeholder="How long do you want this loan for" required>
        </div>
        
        <div class="form-group">
            <label for="interest_percentage">Interest Percentage <small><em>(This is determined by selected employer)</em></small></label>
            <input min="1" max="100" type="number" v-model="interest_percentage" id="interest_percentage" class="form-control"
                name="interest_percentage" 
                placeholder="How many percent return?" readonly required>
        </div>
        
        <div class="form-group">
            <label for="company" style="display:block">Amount Needed <span class="pull-right">Max: <strong>{{max_amount}}</strong></span></label>
            <input type="number" class="form-control" id="amount" v-model="amount" name="amount" required
                placeholder="Enter an amount in Naira" @blur="confirmAmount">
        </div>
    </div>
</template>
<script>
    import { utilitiesMixin } from './../../mixins';
    
    export default {
        props: ["employers", "collectionMethods"],
        
        mixins: [utilitiesMixin],
        
        data() {
            return {
                duration: 1,
                interest_percentage: '',
                amount: 0,
                employer_id: '',
                net_pay: '',
                parsedCollectionMethods: {},
            };
        },
        
        mounted() {
            Object.keys(this.collectionMethods).forEach(key => {
                
                const method = this.collectionMethods[key];
                
                Object.keys(method).forEach(methodKey => {
                    this.parsedCollectionMethods[methodKey]
                        = `${ method[methodKey] } - ${key.toUpperCase()}`;
                });
            });  
        },
        
        methods: {
            confirmAmount() {
                if (this.amount > this.max_amount) {
                    alert(`You cannot take more than ${this.max_amount}`);
                    this.amount = this.round(this.max_amount, 2);
                }
            },
        },
        
        computed: {
            employer() {
                return this.employers.find((employer) => employer.id === this.employer_id);
            },
            
            emi() {
                
                let employer = this.employers.find((employer) => employer.id === this.employer_id);
                
                let feePercentage = 0;
                if (employer) {
                    if (this.duration <= 3) {
                        feePercentage = employer.fees_3;
                    } else if(this.duration > 3 && this.duration <= 6) {
                        feePercentage = employer.fees_6;
                    } else {
                        feePercentage = employer.fees_12;
                    }
                }
                
                feePercentage = parseFloat(feePercentage);
                
                let rate = this.interest_percentage/100;
                let emi = this.pmt(this.amount,rate, this.duration);
              
                let fee = ((feePercentage/100) * this.amount);
                 
                emi += fee;
                
                return typeof emi === 'number' && !isNaN(emi) && isFinite(emi) ? this.round(emi, 2) : 'Loading...';
            },
            
            max_amount() {
                let max_amount = !this.net_pay ? "Loading..." : this.round((this.net_pay * this.duration / 3), 2);
                
                if (this.amount > max_amount)
                    this.amount = max_amount;
                    
                return max_amount;
            },
            
            max_duration() {
                let employer = this.employers.find((employer) => employer.id === this.employer_id);
                return employer ? employer.max_tenure : "Loading";
            }
        },
        
        watch: {
            employer_id: function(current) {
                let employer = this.employers.find((employer) => employer.id === current);
                
                if(employer) {
                    this.interest_percentage = employer.rate_3 || 10;
                }
            },
            
            duration: function(current) {
                let employer = this.employers.find((employer) => employer.id === this.employer_id);
                if (current <= 3) {
                    this.interest_percentage = employer.rate_3;
                } else if(current > 3 && current <= 6) {
                    this.interest_percentage = employer.rate_6;
                } else {
                    this.interest_percentage = employer.rate_12;
                }
                
                if (current > employer.max_tenure) {
                    this.duration = employer.max_tenure;
                }
            }
        }
    };
</script>