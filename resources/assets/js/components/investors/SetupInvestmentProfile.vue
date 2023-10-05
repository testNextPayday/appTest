<template>
    <div>
        <h3>How you want to invest</h3>

        <form method="POST" action="" @submit.prevent="storeProfile" id="setup-investment">

            <div class="pages" v-if="page == 1">
                <h5 > {{page}}) Do you wish your investments to be automatic ? </h5>

                <div class="custom-control custom-control-radio">
                    <input type="radio" name="auto_invest" class="custom-control-input" id="auto_invest_yes" v-model="auto_invest" value="1" :checked="auto_invest == '1'" > 
                    <label class="custom-control-label" for="auto_invest_yes">Yes</label>
                </div>

                <div class="custom-control custom-control-radio">
                    <input type="radio" class="custom-control-input" name="auto_invest" id="auto_invest_no" v-model="auto_invest" value="0" :checked="auto_invest == '0'">
                    <label class="custom-control-label" for="auto_invest_no">No</label>
                </div>
                
                
            </div>

            <div class="pages" v-if="page == 2">
                <h5>{{page}}) Do you wish to auto rollover ? </h5>

                <div class="custom-control custom-control-radio">
                    <input type="radio" class="custom-control-input" id="auto_rollover_yes" name="auto_rollover" value="1" v-model="auto_rollover" :checked="auto_rollover == '1'">
                    <label class="custom-control-label" for="auto_rollover_yes">Yes</label>
                </div>

                <div class="custom-control custom-control-radio">
                   <input type="radio" class="custom-control-input" id="auto_rollover_no" name="auto_rollover" value="0" v-model="auto_rollover" :checked="auto_rollover == '0'">
                    <label class="custom-control-label" for="auto_rollover_no">No</label>
                </div>

                
                
            </div>

            <div class="pages" v-if="page == 3">
                <h5> {{page}}) What Fractions do you want to fund ?</h5>
               
                <div class="custom-control custom-control-radio"  v-for="(fraction,index) in fractions" :key="index">
                    <input type="radio" name="loan_fraction" :id="index" class="custom-control-input" v-model="loan_fraction"  :checked="fraction == loan_fraction" :value="fraction"> 
                    <label class="custom-control-label" :for="index">{{fraction}} % of A single Loan</label>
                </div>
                
            </div>

            <div class="pages" v-if="page == 4">
                <h5> {{page}}) Fund Loans By </h5>
                
                <div class="custom-control custom-checkbox mb-3" v-for="(employer,index) in employers" :key="index">
                    <input type="checkbox" class="custom-control-input" :id="index" name="employer_loan" v-model="employer_loan" :checked=" employer_loan.includes(employer.id) " :value="employer.id" > 
                    <label class="custom-control-label" :for="index">{{employer.name}}</label>
                </div>
                
            </div>

            <div class="pages" v-if="page == 5">
                <h5> {{page}}) When to Expect Paybacks ?</h5>
               
                <div class="custom-control custom-control-radio" v-for="(cycle,index) in cycles" :key="index">
                    <input type="radio" name="payback_cycle" :id="index" class="custom-control-input" v-model="payback_cycle" :checked="cycle == payback_cycle " :value="cycle" > 
                    <label class="custom-control-label" :for="index">{{cycle}}</label>
                </div>
                
            </div>

            <div class="pages" v-if="page == 6">
                <h5> {{page}}) What is the Expected Duration of Loans you want to fund ?</h5>
               
                <div class="custom-control custom-control-radio" v-for="(tenor,index) in tenors" :key="index">
                    <input type="radio" name="loan_tenors" :id="index" class="custom-control-input" v-model="loan_tenors" :checked="tenor == loan_tenors " :value="tenor" > 
                    <label class="custom-control-label" :for="index">{{tenor}} months and above</label>
                </div>
                
            </div>

            <div class="form-group">
                <div v-if="page == maxPageLength">
                    <button type="submit" name="submit" class="btn btn-success float-right"> 
                        <i :class="spinClass"></i>
                        {{buttonText}} </button>
                </div>
                <div v-else>
                    <button type="button" class="btn btn-warning float-right" @click="page = page + 1">Next</button>
                </div>

                 <button  type="button" class="btn btn-primary" v-if="page != 1" @click="page = page - 1">Back</button>
            </div>
        </form>

        <v-tour name="setupInvestmentTour" :steps="steps"></v-tour>

    </div>
</template>
<script>
    import {utilitiesMixin } from './../../mixins';
    export default {
        props : ['investor','employers'],
        name : 'setup-investment-profile',
        mixins : [utilitiesMixin],
        data(){
            return {
                loan_tenors : this.investor.loan_tenors,
                payback_cycle : this.investor.payback_cycle,
                auto_invest : this.investor.auto_invest,
                loan_fraction : this.investor.loan_fraction,
                employer_loan : this.investor.employer_loan === null ? [] : JSON.parse(this.investor.employer_loan),
                auto_rollover : this.investor.auto_rollover,
                fractions : ['25','50','100'],
                cycles : ['Monthly','Quarterly','backend'],
                tenors : ['3','6'],
                page : 1,
                maxPageLength : 6,
                buttonText: "Save Changes",
                steps: [
                    
                    {
                        target: '#setup-investment',
                        content: 'You can now setup your investment profile and automatically allow your funds roll in ',
                        params: {
                        placement: 'top' // Any valid Popper.js placement. See https://popper.js.org/popper-documentation.html#Popper.placements
                        }
                    }
                    ]
            };
        },
        mounted(){
            this.$tours['setupInvestmentTour'].start();
        },
        methods : {

            async storeProfile(){

                this.startLoading();
                this.buttonText = "Saving ...";
                const request = {
                    loan_tenors : this.loan_tenors,
                    payback_cycle : this.payback_cycle,
                    auto_invest : this.auto_invest,
                    loan_fraction : this.loan_fraction,
                    employer_loan : JSON.stringify(this.employer_loan),
                    auto_rollover : this.auto_rollover
                }

                await axios.post('/investor/setup-investment-profile',request).then((res)=>{
                     this.alertSuccess('Successfully saved');
                    this.stopLoading();
                    this.buttonText = "Save Changes";
                }).catch((e)=>{
                    this.alertError(e);
                    this.stopLoading();
                });
            }
        }
    }
</script>
<style>
    .pages {
        padding:10px;
    }
</style>
