<template>

    <div>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#loanRestructure">Restructure Loan</button>

        <!-- Modal -->
        <div class="modal fade" id="loanRestructure" tabindex="-1" role="dialog" aria-labelledby="loanRestructureLabel" aria-hidden="true">

            <div class="modal-dialog" role="document">

                <div class="modal-content">

                    <div class="modal-header">

                        <h3 class="modal-title" id="loanRestructureLabel">Loan Restructurer</h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                            <span aria-hidden="true">&times;</span>

                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="pages" v-if=" currentPage == 1">

                            <h3 class="page-title">SetUp Restructuring</h3>

                            <form method="POST"  @submit.prevent="event.preventDefault()">
                            
                                <div class="row">

                                    <label class="form-control-label col-md-6">Current EMI : <b>{{formatAsCurrency(oldemi)}}</b></label>

                                    <label class="form-control-label col-md-6">Duration(s) Left : <b>{{unpaidplans}}</b></label>

                                </div>
                                
                                <div class="row">

                                    <div class="form-group col-md-4">

                                        <label class="form-control-label">Enter a desired Duration</label>

                                        <div class="input-group">

                                            <input v-model.number="duration" class="form-control" type="number" name="duration">

                                                <div class="input-group-addon">

                                                        <button type="button" class="btn btn-sm " @click="autoGenerate">
                                                            Generate Random
                                                        </button>

                                                </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">

                                        <label class="form-control-label">Add Penalty Interest</label>

                                        <div class="input-group">

                                            <input v-model.number="charge" class="form-control" type="number" name="charge">

                                        </div>
                                        
                                    </div>

                                    <div class="form-group col-md-4">

                                        <label class="form-control-label">Additional Amount</label>

                                        <div class="input-group">

                                            <input v-model.number="addedAmount" class="form-control" type="number" name="addedAmount">

                                        </div>
                                        
                                    </div>

                                </div>

                                <div class="row">

                                    <table class="table table-bordered">

                                        <thead></thead>

                                        <tbody>

                                            <tr>

                                                <td>Sum of Unpaid EMI(s)</td>

                                                <td>{{formatAsCurrency(Math.round(unpaidemi,2))}}</td>

                                            </tr>

                                            <tr>

                                                <td>Interest Rate </td>

                                                <td>{{rate}}% </td>

                                            </tr>

                                            <tr>
                                                <td>Additional Duration</td>

                                                <td>{{duration}}</td>

                                            </tr>

                                                <tr>

                                                    <td>Added Penal Charge ({{charge}}% of {{formatAsCurrency(Math.round(unpaidemi,2))}})</td>

                                                    <td>{{formatAsCurrency(Math.round(penalCharge,2))}}</td>

                                                </tr>


                                            <tr>

                                                <td>Monthly Payments</td>

                                                <td>{{formatAsCurrency(monthly)}}</td>

                                            </tr>

                                        </tbody>

                                        <tfoot>

                                            <tr>

                                                <td><b>New EMI</b></td>

                                                <td>{{formatAsCurrency(emi)}}</td>

                                            </tr>

                                        </tfoot>
                                    </table>
                                    
                                </div>


                                <div class="text-center">
                                    <br>
                                    <button class="btn btn-danger" @click.prevent="RescheduleLoan"><i :class="spinClass"></i>{{rescheduleButtonText}}</button>
                                    <br>
                                </div>
                                
                            </form>

                        
                        </div>

                        <div class="pages" v-if="currentPage == 2">

                            <h3 class="page-title">Setup Loan Collection</h3>
                            
                            <form method="post" :action="collection_url" @submit.prevent="Setup">

                                
                                <div class="row justify-content-center">

                                    <div class="col-sm-8">
                                        
                                        <div class="row">

                                            <div class="col-sm-12">

                                                <h5 class="badge badge-primary">Primary Collection</h5>

                                            </div>
                                            
                                            <br/><br/>
                                        </div>
                                        
                                        <collection-plan-juggler
                                            :collection-plans="collection_plans"
                                            :field-name="'collection_plan'"
                                            :selected="'employer_primary_collection_plan'"
                                            @primaryPlanChanged="updatePrimaryCollectionPlan">
                                        </collection-plan-juggler>
                                        
                                        <br/><br/>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <h5 class="badge badge-danger">Secondary Collection</h5>
                                            </div>
                                            <br/><br/>
                                        </div>
                                        <collection-plan-juggler
                                            :collection-plans="collection_plans"
                                            :field-name="'collection_plan_secondary'"
                                            :selected="employer_secondary_collection_plan"
                                            is-secondary
                                            @secondaryPlanChanged="updateSecondaryCollectionPlan">
                                        </collection-plan-juggler>
                                        <br/><br/>
                                                
                                    </div>
                                    
                                </div>

                                
                                <div class="text-right">
                                    <button class="btn btn-danger"> <i :class="spinClass"></i> {{setupButtonText}}</button>
                                </div>
                            </form>

                        </div>

                        <div class="navigations">

                            <div class="float-right">

                                <button type="button" class="btn btn-info" @click.prevent="currentPage++" v-if="currentPage < maxPages">Skip to Setup</button>
                            </div>
                        
                            <button type="button" class="btn btn-primary" @click.prevent="currentPage--" v-if="currentPage > 1">Back</button>

                        </div>
                        
                    </div>

                    <div class="modal-footer">
                        
                    </div>

                </div>
            </div>

        </div>

    </div>

</template>

<script>

import { utilitiesMixin } from './../../mixins';

export default {

    name : 'restructure-loan',

    mixins: [utilitiesMixin],

    props : [

        "loan",
        "rate",
        "unpaidemi",
        "url",
        "oldemi",
        "unpaidplans",
        "collection_plans",
        "employer_secondary_collection_plan",
        "employer_primary_collection_plan",
        "collection_url"
    ],

    data(){
        
        return {
            duration : 0,
            charge : 0,
            addedAmount : 0,
            currentPage : 1,
            maxPages : 2,
            isRescheduled : false,
            rescheduleButtonText : 'Reschedule Loan',
            setupButtonText : 'Setup Loan',
            primaryCollectionPlan : '',
            secondaryCollectionPlan : ''
        };
    },
    methods : {

       autoGenerate(){

           this.duration = Math.ceil(this.generateRandom(3,12));
       },

       updatePrimaryCollectionPlan(value){
           
           this.primaryCollectionPlan = value;
       },

       updateSecondaryCollectionPlan(value){

           this.secondaryCollectionPlan = value;
       },

       async RescheduleLoan(){

           this.startLoading();

           this.rescheduleButtonText = 'Rescheduling Loan ...';

           const request = { charge : this.penalCharge , duration : this.duration,addedAmount : this.addedAmount};
          

           await axios.post(this.url,request).then((res)=>{
               
               this.stopLoading();

               this.rescheduleButtonText = 'Loan Rescheduled';

               this.alertSuccess('Loan successfully Rescheduled');

               this.currentPage = 2;

           }).catch((e)=>{

               this.stopLoading();

               this.rescheduleButtonText = 'Rescheduling Failed';

               this.handleApiErrors(e);
           });
           
       },

       async Setup(){
           
           this.startLoading();

           this.setupButtonText = 'Setting Up Loan ...';

            const request = { 

                collection_plan : this.primaryCollectionPlan, 

                collection_plan_secondary : this.secondaryCollectionPlan

            };

           await axios.post(this.collection_url,request).then((res)=> {

               this.stopLoading();

               this.setupButtonText = 'Setup Complete';

               this.alertSuccess('Loan Setup Completed');

           }).catch((e) => {

               this.stopLoading();

               this.setupButtonText = 'Try Again. ';

               this.handleApiErrors(e);

           })

       }
       

    },
    computed : {

        monthsPaid : function(){

            return this.loan.repayment_plans.filter((plan)=>{

               return plan.status == 0;

            }).length;
        },

        penalCharge: function(){

            return (this.charge /100) * this.unpaidemi;
        },

        emi : function(){

            return this.monthly;

        },

        monthly : function(){

            var rate = (this.rate + this.charge) / 100;

            var amount = this.unpaidemi + this.addedAmount + this.penalCharge;

            return this.pmt(amount,rate,this.duration)
           
        }
       
    },
    watch : {
       
        
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

.modal-dialog {
    margin-top : 5px;
}

.modal .modal-dialog .modal-content .modal-body {
    padding-top:30px;
}

</style>