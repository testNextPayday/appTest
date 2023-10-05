<template>
    <div style="display:inline">

        <button data-toggle="modal" class="btn btn-sm btn-warning" data-target="#pay-installments">Make Installmental Payments</button>

        <!-- Modal -->
        <div class="modal fade" id="pay-installments" tabindex="-1" role="dialog" aria-labelledby="loanRestructureLabel" aria-hidden="true">

            <div class="modal-dialog" role="document">

                <div class="modal-content" v-if="showContent">

                    <div class="modal-header">

                        <!-- <h3 class="modal-title" id="loanRestructureLabel">Loan Restructurer</h3> -->

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                            <span aria-hidden="true">&times;</span>

                        </button>

                    </div>

                    <div class="modal-body">

                        <div>
                            <table class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Amount</th>
                                        <th>Month</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(plan,index) in plans" :key="index">
                                        <td>
                                            
                                             <div class="custom-control custom-checkbox mb-3">
                                                <input type="checkbox" class="custom-control-input" @change="setAmount" :data-details="JSON.stringify({amount:plan.emi, id:plan.id})" :id="index"  v-model="paymentBagIndex" :value="index" >
                                                  <label class="custom-control-label" :for="index">Plan</label> 
                                            </div>
                                        </td>

                                        <td>{{formatAsCurrency(plan.emi)}}</td>

                                        <td>{{plan.month_no}} Installment</td>

                                        <td>{{plan.payday}}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>

                        <div>
                            <span>Total Amount to be paid: ₦ {{formatAsCurrency(totalAmount)}}</span>
                        </div>

                        <button class="btn btn-primary " @click="goToGateway" v-if="totalAmount > 0"> Pay ₦ {{formatAsCurrency(totalAmount)}}</button>
                        <paystack :pay-key="paykey" ref="paystack" :email="user.email" :amount="totalAmount" @paystack-response="paystackResponse" :reference="reference"></paystack>

                    </div>
                    

                    <div class="modal-footer">
                      
                    </div>

                </div>

                <div class="modal-content modified-content" v-if="verifyingTransaction">
                    <div class="modal-body ">
                         <newton-loader  :text="'Verifying Transaction...'">
                        </newton-loader>
                    </div>
                   
                </div>
                  

                <div class="modal-content modified-content" v-if="updatingRepaymentPlans">
                    <div class="modal-body ">
                        <newton-loader  :text="'Updating payment...'">
                        </newton-loader>
                    </div>
                    
                </div>

            </div>

        </div>

    </div>
</template>
<script>

import Paystack from '../Paystack';
import { utilitiesMixin } from './../../mixins';

export default {

    mixins: [utilitiesMixin],

    components : {
        'paystack' : Paystack
    },

    props : {

        plans : {
            type: Array,
            required:true
        },

        user : {
            type:Object,
            required :true
        },

        paykey : {
            type:String,
            required : true
        }
    },

    data(){

        return {

            paymentBagIndex : [],

            totalAmount : 0,

            reference : '',

            paidIds : [],

            verifyingTransaction : false,

            updatingRepaymentPlans : false,

            showContent : true
            
        };
    },

    watch : {

        verifyingTransaction : function(value){

            if (value == false && this.updatingRepaymentPlans == false){

                this.showContent = true;
            }else {

                this.showContent = false;
            }
        },

        updatingRepaymentPlans : function(value){

             if (value == false && this.verifyingTransaction == false){

                this.showContent = true;
            }else {

                this.showContent = false;
            }
        }
    },  

    mounted(){

        this.getTransReference();
    },

    methods : {

        setAmount(e){

            var details = JSON.parse(e.currentTarget.getAttribute("data-details"));

            var checked = e.currentTarget.checked;

            var amount = Math.round(details.amount, 2);

            var id = details.id;

            if(checked) {

                this.totalAmount += amount;

                this.paidIds.push(id);

            }else {

                this.totalAmount -= amount;
                // remove from month list also

                var pos = this.paidIds.indexOf(id);

                this.paidIds.splice(pos, 1);

                }
        },
         paystackResponse(event) {

            this.verifyingTransaction = true;
            
            const request = {reference : event.reference , amount : this.totalAmount}

            axios.post(`/paystack/verify/transaction/repaymentplan`, request).then(response => {

                 this.verifyingTransaction = false;

                if(response.data.status == 1) {

                   
                    return this.UpdateRepaymentPlans();

                } else {

                    this.alertError(response.data.message);
                }   
              
            }).catch(error => {

                this.alertError('An error occurred. Please try again');

            });

               
        },

        async UpdateRepaymentPlans(){

            this.updatingRepaymentPlans = true;

            const request = {paymentIds : this.paidIds};

            await axios.post(`/plans/update/payment`, request).then((res)=>{

                this.alertSuccess(res.data);

                location.reload(true);

            }).catch((e)=>{

                this.alertError(e.response.data);
            });

            this.updatingRepaymentPlans = false;

            
        },

        async goToGateway() {

            this.startLoading();

            await this.$refs.paystack.initiatePayment();

            this.stopLoading();

        },

        async getTransReference(){

            await axios.get('/paystack/reference').then((res)=> {

                this.reference = res.data;
            })
        }
    },

    computed : {

        
        
    }
}
</script>

<style>

@media (min-width: 576px){

    .modal-dialog {
    max-width: 700px;
    margin: 30px auto;

    }
}

.modified-content {
    width: 60%;
    margin: auto;
    margin-top: 30%;
    height: 200px;
}

.modal-dialog {
    margin-top : 5px;
}

.modal .modal-dialog .modal-content .modal-body {
    padding-top:30px;
}

</style>