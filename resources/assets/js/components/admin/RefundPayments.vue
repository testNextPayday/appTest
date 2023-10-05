<template>

    <div>

        <div v-if="loading">
            <newton-loader></newton-loader>
        </div>

        <div v-else>
           
            <div class="row" v-if="refunds.length > 0">

                <div class="col-md-10" v-if="screenView == 'list'">

                    <div class="card">

                        <div class="card-header">
                            Refunds
                            <div class="col-sm-3" style="display:inline-block">
                                <form >
                                    <input class="form-control" v-model="search" @keyup="filterRefunds" placeholder="Enter Refund" value="" required/>
                                </form>
                            </div>

                        </div>

                        <div class="card-body">

                             <table class="table table-responsive-sm table-hover table-outline mb-0">

                                <thead class="thead-light">

                                    <tr>
                                        <th>Status</th>
                                        <th>Borrower Name</th>
                                        <th>Loan</th>
                                        
                                        <th>Amount</th>
                                       
                                    </tr>

                                </thead>

                                <tbody>
                                    
                                    
                                    <tr v-for="(item,index) in refunds" :key="index" @click="viewRefund" :data-index="index">
                                        
                                        <td>
                                            <span v-if="item.status == 0"><i class="fa fa-circle text-warning"></i></span>
                                            <span v-if="item.status == 1"><i class="fa fa-circle text-success"></i></span>
                                            <span v-if="item.status == 2"><i class="fa fa-circle text-danger"></i></span>
                                        </td>

                                        <td>
                                            {{item.user.name}}
                                        </td>
                                        <td>
                                            {{item.loan.reference}} - ({{item.loan.amount}})
                                        </td>

                                        <td>
                                            {{formatAsCurrency(parseFloat(item.amount))}}
                                        </td>

                                    </tr>
                                    
                                    
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
                <!--/.col-->

                <div class="col-md-12" v-if="screenView == 'single'">

                    <div class="row">

                        <div class="col-md-6 card">

                            <div class="card-header">

                                <h4 @click="viewList" style="cursor:pointer"><i class="fa  fa-arrow-left"> </i> Back</h4>

                            </div>

                            <div class="card-body">

                                <div class="object-header">

                                    <div class="pull-right">

                                        <span class="badge badge-success" v-if="currentRefund.status == 1">Approved</span>

                                        <span class="badge badge-warning" v-if="currentRefund.status == 0">Pending</span>

                                        <span class="badge badge-warning" v-if="currentRefund.status == 2">Decline</span>

                                    </div>

                                </div>

                                <div class="list-group no-radius no-bg border-top">

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Borrower Name</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span >{{currentRefund.user.name}}</span></div>

                                    </div>

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Amount Refunded</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span >{{currentRefund.amount}}</span></div>

                                    </div>

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Reason for Refund</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span >{{currentRefund.reason}}</span></div>

                                    </div>

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Staff Added</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span>{{currentRefund.staff.firstname}}  {{currentRefund.staff.lastname}}</span></div>

                                    </div>

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Created On</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span>{{currentRefund.created_at}}</span></div>

                                    </div>

                                </div>
                                                

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="card-header">Refund Transactions </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="row" v-else>
                
                <div class="col-md-12" style="height:450px">

                    <div class="text-center" style="margin-top:150px">

                         <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#newRefund">
                            <i class="fa fa-plus"></i>
                            Add new
                        </button>

                        <p style="font-size:120%;">You have not created any Refunds yet</p>
                    </div>
                    

                </div>

            </div>


           

        </div>

       
    </div>
</template>
<script>

import { utilitiesMixin } from './../../mixins';

export default {

    mixins: [utilitiesMixin],

    props : {

        refunds : {
            type : Array,
            required : true
        }
    },

    data(){

        return {

            screenView : 'list',

            currentRefund : {

            },

            loading : false,

            search : '',

            RefundTransactions : [],

           

        }
    },

    computed : {

       
    },


    methods : {

        filterRefunds(){

            return this.Refunds = this.Refunds.filter((Refund)=>Refund.name.indexOf(this.search));
        },

        

        async getTransactions(){

            var Refund = this.currentRefund;

            await axios.get(`/ucnull/Refunds/transactions/${Refund.id}`).then((res)=> {

                this.RefundTransactions = res.data;

            }).catch((e)=> {

                this.handleApiErrors(e);
            })
        },

        viewRefund(e) {
          
            var index = e.currentTarget.getAttribute('data-index');

            this.currentRefund  = this.refunds[index];

            this.screenView  = 'single';

        },

        viewList()
        {
            this.currentRefund = '';

            this.screenView = 'list';
        },

       
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

tr {
    cursor : pointer;
}

</style>