<template>

    <div>



        <div v-if="loading">
            <newton-loader></newton-loader>
        </div>

        <div v-else>

            <div class="row">



                <div class="col-md-12" v-if="screenView == 'list'">

                    <div class="card">

                        <div class="card-header row">



                            <div class="col-md-6 grid-margin offset-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-0">{{transactions.length}} Transactions Displayed</h4>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-inline-block pt-3">
                                                <div class="d-flex">
                                                    <h2 class="mb-0">₦{{formatAsCurrency(sumTransactions)}}</h2>
                                                    <div class="d-none d-md-flex align-items-center ml-2">
                                                    <!--<i class="mdi mdi-clock text-muted"></i>-->
                                                    <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="d-inline-block">
                                                <div class="bg-danger px-4 py-2 rounded">
                                                    <i class="icon-badge text-white icon-lg"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12" style="display:inline-block">

                                <form @submit.prevent="searchTransactions">

                                    <div class="form-row">

                                        <input class="form-control col-md-2" type="date" v-model="startDate"  title="Enter start date" required/>

                                         <input class="form-control col-md-2" type="date" v-model="endDate"  title="Enter End date" required/>

                                        <select class="form-control col-md-2" v-model="searchBy" title="Search By Activity">
                                            <option value="All">All</option>
                                            <option value="refund">Refunds</option>
                                            <option value="loan">Loans Disbursed</option>
                                            <option value="withdrawalrequest">Withdrawal Request</option>
                                            <option value="staff">Staff Salaries</option>
                                            <option value="bill">Bills</option>
                                        </select>

                                        <select class="form-control col-md-2" v-model="searchStatus" title="Search By Status">
                                            <option value="All">All</option>
                                            <option value="success">Successful</option>
                                            <option value="failed">Failed</option>
                                            <option value="pending">Pending</option>
                                            <option value="otp">Otp Pending</option>
                                        </select>

                                        <button type="submit" name="submit" class="col-md-2 btn btn-primary btn-sm"> <i :class="spinClass"></i> {{searchButton}}</button>

                                    </div>

                                </form>

                            </div>

                        </div>

                        <div class="card-body">

                            <table class="table table-responsive-sm table-hover table-outline mb-0" >

                                <thead class="thead-light">

                                    <tr>
                                        <th>Title</th>
                                        <th>Collection Method</th>
                                        <th>#Reference</th>
                                        <th>Amount</th>
                                        <th>Status Text</th>
                                        <th>Date</th>

                                    </tr>

                                </thead>

                                <tbody v-if="transactions.length > 0">

                                    <gateway-transaction @viewtransaction ="viewTransaction"  v-for="(transaction,index) in transactions" :key="index" :data-index="index" :transaction="transaction" :showMore="true"></gateway-transaction>

                                </tbody>

                                <tbody v-else>

                                    <tr>
                                        <td colspan="3" class="text-center"> No Transactions available</td>
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

                                        <span class="badge badge-success" v-if="currentTransaction.status_message == 'success'">Success</span>

                                        <span class="badge badge-danger" v-else-if="currentTransaction.status_message == 'failed'">Failed</span>

                                        <span class="badge badge-warning" v-else>In Progress</span>

                                    </div>

                                     <div><h4>#{{currentTransaction.reference}}({{currentTransaction.description}})</h4></div>

                                </div>

                                <div class="list-group no-radius no-bg border-top">

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Transaction Amount</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span >{{currentTransaction.amount}}</span></div>

                                    </div>

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Transaction ID</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span >{{currentTransaction.transaction_id}}</span></div>

                                    </div>

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Transaction Status</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span>{{currentTransaction.status_message}}</span></div>

                                    </div>

                                     <div class="list-group-item">

                                        <div class="col-xs-5">Created On</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span>{{currentTransaction.created_at}}</span></div>

                                    </div>

                                </div>

                                <div class="text-center mt-3">



                                </div>


                            </div>

                            <div class="card-footer">

                                <h4>Actions</h4>

                                <transaction-actions :proptransaction="currentTransaction"></transaction-actions>
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="card-header">Transactions Linked To

                                <div class="pull-right"><a href="#"  class="badge badge-success">More Details</a></div>

                            </div>


                            <div class="card-body">

                                    <div class="list-group no-radius no-bg border-top" >

                                        <div class="list-group-item">

                                            <div class="col-xs-5"> Type</div>

                                            <div class="col-xs-7 text-right text-capitalize font-bold badge-primary badge"><span >{{currentTransaction.link_type.substr(11)}}</span></div>

                                        </div>

                                        <div class="list-group-item" v-for="(attr,index) in linkDataViewables" :key="index" v-show="currentTransaction.link.hasOwnProperty(attr)">

                                            <div class="col-xs-5 text-capitalize" v-if="currentTransaction.link.hasOwnProperty(attr)">{{attr}}</div>

                                            <div class="col-xs-7 text-right text-capitalize font-bold" v-if="currentTransaction.link.hasOwnProperty(attr)"><span >{{currentTransaction.link[attr]}}</span></div>

                                        </div>

                                    </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


    </div>
</template>
<script>

import { utilitiesMixin } from './../../mixins';

import TransactionActions from './TransactionActions';


export default {

    mixins: [utilitiesMixin],

    components  : {

        'transaction-actions' : TransactionActions
    },


    data(){

        return {

            screenView : 'list',

            currentTransaction : {

            },

            loading : false,

            searchBy : 'All',

            searchStatus : 'All',

            endDate : '',

            startDate : '',

            viewableTransactions : [],

            store : [],

            searchButton : 'Search',

            linkDataViewables  : ['name','amount','reference','firstname','lastname','salary','phone','disbursal_amount','reason']

        }
    },
    created(){

    },

    watch: {

    },

    computed : {

        transactions: {

            get(){

                return this.viewableTransactions;
            },
            set(newValue){

                this.viewableTransactions = newValue;
            }
        },

        sumTransactions(){

            var amount = 0;
            this.transactions.forEach((obj)=> amount += obj.amount);
            return amount;
        }

    },


    methods : {

        async searchTransactions(){

            this.startLoading();

            this.searchButton = 'Searching..';

            const request  = {startDate : this.startDate, endDate : this.endDate, searchBy : this.searchBy, searchStatus : this.searchStatus};

            await this.$store.dispatch('searchTransactions',request)

            this.transactions = this.$store.state.transactions;

            this.searchButton = 'Search';

           this.stopLoading();

        },

        viewTransaction(e) {

            var index = e.currentTarget.getAttribute('data-index');

            this.currentTransaction  = this.transactions[index];

            this.screenView  = 'single';

        },

        viewList()
        {
            this.currentTransaction = '';

            this.TransactionTransactions = [];

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

#transactions-table  tr td , #transactions-table tr th{

    font-size: 12px;
}

</style>