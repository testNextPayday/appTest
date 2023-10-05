<template>
    <div style="display:inline">
        <button data-toggle="modal" data-target="#resubmit-loan-request" class="btn btn-success btn-sm">Resubmit Request</button>
        <div class="modal fade" id="resubmit-loan-request" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel-2">Resubmit Loan Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form :action="url" method="POST">
                            <input type="hidden" name="_token" :value="token">

                            <input type="hidden" name="charge" :value="charge">

                            <div class="form-group">
                                <label class="form-control-label" style="display:block;text-align:initial">New Amount</label>
                                <input type="text" name="newAmount" class="form-control" v-model.number="newAmount">
                            </div>


                            <div class="form-group">
                                <label class="form-control-label" style="display:block;text-align:initial">New Duration</label>
                                <input type="text" name="duration" class="form-control" v-model="duration">
                            </div>

                           <template v-if="loandocs.bank_statement == true">
                                <div class="form-group">
                                    <label for="bank_statement" class="form-control-label" style="display:block;text-align:initial">Bank Statement/Pay Slip (3 months from today, JPG or PDF)</label>
                                    <input type="file" class="form-control" name="bank_statement" id="bank_statement" required>
                                </div>  
                            </template>

                             <div class="form-group">
                                <div class="mb-2 mt-2" style="font-size:0.9rem;text-align:initial">
                                    <i class="icon-check text-success"></i> Gross loan
                                    <span>
                                        ₦{{grossloan.toLocaleString('en', {maximumFractionDigits: 2})}}
                                    </span>
                                </div>      
                            </div>

                            <div class="form-group">
                                <div class="mb-2 mt-2" style="font-size:0.9rem;text-align:initial">
                                    <i class="icon-check text-success"></i> Loan fee
                                    <span>
                                        ₦{{charge == 0 ? '0' : charge.toLocaleString('en', {maximumFractionDigits: 2}) }} 
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="mb-3 mt-2" style="font-size:0.9rem;text-align:initial">
                                <i class="icon-check text-success"></i> Disbursal amount
                                <span>
                                    ₦{{disbursal == 0 ? '0' : disbursal.toLocaleString('en', {maximumFractionDigits: 2}) }}
                                </span> 
                                </div>
                            </div>


                            <div class="row mb-2">
                                <div class="col">
                                    <div class="float-right font-weight-bold">
                                    <label class="active">
                                        <input type="radio" value="1" v-model="setoff" required @change="getAmount"> Set-off
                                    </label>

                                    <label class="">
                                        <input type="radio" value="0" v-model="setoff" required @change="getAmount"> Capitalize 
                                    </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button class="btn btn-primary">Submit</button>
                            </div>

                        </form>
                    </div>
                    <!--<div class="modal-footer">-->
                    <!--    <button type="button" class="btn btn-success">Submit</button>-->
                    <!--    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { utilitiesMixin } from "../../mixins";

export default {

    mixins: [utilitiesMixin],
    props : {
        loanrequest : {
            type : Object,
            required : true
        },

        url  : { 
            type : String,
            required : true
        },

        token : {
            type : String,
            required : true
        }
    },
    data() {

        return {
           
            newAmount : parseFloat(this.loanrequest.amount, 2).toFixed(2),
            duration : this.loanrequest.duration,
            employer : {},
            loandocs : {},
            charge : 0,
            setoff : '1',
            disbursal : 0,
            loanlimit : 0,
            grossloan : 0

        };
    },

    mounted() {

        this.employer = this.loanrequest.employment.employer;

        this.loandocs = JSON.parse(this.employer.loanRequestDocs);

        this.charge = this.newLoan(this.loanrequest.amount).charge;

        this.grossloan = parseFloat(this.newAmount);

        this.loanlimit = this.employer.loan_limit;

        this.disbursal  = this.grossloan - this.charge;
    },

    watch : {

        newAmount : function(newvalue, oldvalue) {

            if (newvalue > this.loanlimit) {
               
               this.alertError('Cannot take more than employer loan limit');

               this.newAmount = oldvalue;

               return false;
            }
            
             this.grossloan = parseFloat(newvalue);

             this.charge = this.newLoan(this.grossloan).charge;

             this.getAmount();
        }
    },

    methods : {

        newLoan(requestAmount) {
            let percentage = this.employer.success_fee/100;
            let num = 1 - percentage;
            let loanAmount = requestAmount/num;
            let charge = loanAmount - requestAmount;
            return {loanAmount, charge};
        },

        getAmount() {
            if (this.setoff == '1') {

                //setting off charge from amount
                this.grossloan -= this.charge;
            }

            if (this.setoff == '0') {
                // we are capitalizing our charge
                this.grossloan += this.charge;
                
            }

            this.disbursal = this.grossloan - this.charge;
        }
    }
}
</script>