<template>

    <div>

        <div v-if="loading">
            <newton-loader></newton-loader>
        </div>

        <div v-else>
           
            <div class="row">

                <div class="col-md-12" v-if="screenView == 'list'">

                    <div class="card" style="height:500px; overflow-y:scroll;">

                        <div class="card-header">
                            Bills
                            <div class="col-sm-3" style="display:inline-block">
                                <form >
                                    <input class="form-control" v-model="search"  placeholder="Enter Bill" value="" required/>
                                </form>
                            </div>

                            <div class="pull-right">

                                <!-- <form @submit.prevent="payAllBills" style="display: inline" v-if="activeBills.length && isAdmin">
                                    <button class="btn btn-xs btn-danger" type="submit">Pay ({{activeBills.length}}) Active Bill(s)</button>
                                </form> -->
                                <category-manager></category-manager>

                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#newbill">
                                    <i class="fa fa-plus"></i>
                                    Add new Bill
                                </button>
                            </div>

                        </div>

                        <div class="card-body">

                            <table id = "order-listing" class="table table-responsive-sm table-hover table-outline mb-0" >

                                <thead class="thead-light">

                                    <tr>
                                        <th>Status</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Category</th>
                                        <th>Date</th>
                                        <th>Automated</th>
                                       
                                    </tr>

                                </thead>

                                <tbody v-if="bills.length > 0"> 
                                  
                                   
                                    <tr v-for="(bill,index) in bills" :key="index" @click="viewBill" :data-index="index" >
                                       
                                        <td>
                                            <span v-if="bill.status == 0"><i class="fa fa-circle text-danger"></i></span>
                                            <span v-if="bill.status == 1"><i class="fa fa-circle text-success"></i></span>
                                        </td>
                                        <td>
                                            {{bill.name}}
                                        </td>
                                        <td>
                                            {{formatAsCurrency(parseFloat(bill.amount))}}
                                        </td>
                                        <td>
                                            {{bill.category}}
                                        </td>
                                        

                                        <td>
                                            {{bill.created_at}}
                                        </td>

                                        <td>
                                            <span v-if="bill.frequency == 'always'" class="badge badge-danger"> Yes (Recurring {{bill.occurs}}) </span>

                                            <span v-else class="badge badge-success">  No  </span>
                                        </td>
                                        
                                        
                                    </tr>
                                  
                                    
                                </tbody>

                               

                                <tbody v-else>

                                    <tr>
                                        <td colspan="3" class="text-center"> No Bills available</td>
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

                                        <span class="badge badge-success" v-if="currentBill.status == 1">Active</span>

                                        <span class="badge badge-danger" v-if="currentBill.status == 0">Inactive</span>


                                    </div>

                                     <div><h4>{{currentBill.name}}</h4></div>

                                </div>

                                <div class="list-group no-radius no-bg border-top">

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Bill Amount</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span >{{currentBill.amount}}</span></div>

                                    </div>

                                    <!-- <div class="list-group-item">

                                        <div class="col-xs-5">Bill Nature</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span >{{currentBill.frequency}}</span></div>

                                    </div> -->

                                

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Bill Interval</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold">
                                            <span>{{currentBill.occurs}}</span>

                                            <span v-if="currentBill.frequency == 'always'" class="badge badge-danger"> Recurring </span>
                                        </div>

                                    </div>

                                     <div class="list-group-item">

                                        <div class="col-xs-5">Created On</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span>{{currentBill.created_at}}</span></div>

                                    </div>

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Bill Account Number</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span>{{currentBill.account_number}}</span></div>

                                    </div>

                                    <div class="list-group-item">

                                        <div class="col-xs-5">Bill Bank Name</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold"><span>{{currentBill.bank_name}}</span></div>

                                    </div>


                                    <div class="list-group-item">

                                        <div class="col-xs-5">Payment Recipient Code</div>

                                        <div class="col-xs-7 text-right text-capitalize font-bold">

                                            <span v-if="currentBill.recipient_code">{{currentBill.recipient_code}}</span>

                                            <form @submit.prevent="createRecipient" v-else>

                                                 <newton-loader v-if="isCreatingRecipient"></newton-loader>
                                                <button type="submit" class="btn btn-xs btn-primary" v-else>Create Recipient</button>
                                            </form>

                                           

                                        </div>

                                    </div>

                                    

                                </div>

                                <div class="text-center mt-3">

                                    <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#updatebill"> <i class="fa fa-edit"></i> Edit Bill</button>

                                    <button class="btn btn-danger btn-sm" @click="deleteBill"> <i class="fa fa-trash"></i> Delete Bill</button>

                                    <button class="btn btn-warning btn-sm" @click="requestPayment" > <i class="fa fa-credit-card"></i> Request Payment</button>

                                </div>
                               

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="card-header">Bill Transactions 

                                <div class="pull-right"><a href="#" @click="getTransactions" class="badge badge-success">View Transactions</a></div>
                            </div>

                            <div class="card-body">

                                <table class="table table-responsive table-bordered" id="transactions-table">

                                    <thead>
                                        <tr>
                                          
                                            <th>Amount</th>
                                            <th>Status Text</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody v-if="billTransactions.length > 0">

                                        <gateway-transaction v-for="(transaction,index) in billTransactions" :key="index" :transaction="transaction"></gateway-transaction>

                                    </tbody>

                                    <tbody v-else>

                                        <tr v-if="isFetchingTransactions">

                                            <td colspan="4">
                                                <newton-loader></newton-loader>
                                            </td>

                                        </tr>

                                        <tr v-else>
                                            <td colspan="4">No Transactions Available</td>
                                        </tr>

                                    </tbody>
                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        

            <div class="modal fade" id="newbill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

                <div class="modal-dialog modal-sm" role="document">

                        <div class="modal-content">

                            <div class="modal-header">

                                <h4 class="modal-title">Add new bill</h4><br>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>

                            </div>
                    
                            <form method="post" @submit.prevent="createNewBill">
                        
                                <div class="modal-body">

                                    <div class="form-group mb-3">

                                        <label for="bill-name" class="form-control-label">Bill Name</label>
                                        <input type="text" name="bill-name" id="bill-name" v-model="newBill.name" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="bill-amount" class="form-control-label">Bill Amount</label>
                                        <input type="number" name="bill-amount" id="bill-amount" v-model="newBill.amount" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group mb-3">

                                        <label for="bill-category" class="form-control-label">Bill Category</label>

                                        <select name="bill-category" id="bill-category" v-model="newBill.occurs" class="form-control" required>

                                            <option v-if="billcategories.length < 1" value="null">No Category Available</option>
                                            <template v-else>
                                                <option v-for="(category, index) in billcategories" :key="index" :value="category.id">{{category.name}}</option>
                                            </template>

                                        </select>

                                    </div>

                                    <!-- <div class="form-group mb-3">

                                        <label for="bill-frequency" class="form-control-label">Repeat Bill for Every Occurance</label>

                                        <select name="bill-frequency" id="bill-frequency" v-model="newBill.frequency" class="form-control" required>

                                            <option value="once">Once</option>
                                            <option value="always">Always</option>

                                        </select>
                                        
                                    </div> -->

                                    <div class="form-group mb-3">

                                        <label for="bill-frequency" class="form-control-label">Bank</label>

                                        <select name="bill-frequency" id="bill-frequency" @change="isAccountResolved = false " v-model="newBill.bank_code" class="form-control" required>
                                            <option value="">Choose a Bank .....</option>
                                            <!-- <option :value="code" v-for="(code,index) in Object.keys(banks)" :key="index">{{banks[code]}}</option> -->
                                            <option  v-for="bank in banks" :value="bank.code">{{bank.name}}</option>
                                            <!-- <option v-for="bank in banks" :value="bank[0]">{{ bank[0] }}</option> -->


                                        </select>
                                        
                                    </div>

                                    <div class="form-group mb-3">

                                        <label for="account-number" class="form-control-label">Account Number</label>

                                        <input type="number" name="account-number" id="account-number" @change="isAccountResolved = false "  v-model="newBill.account_number" class="form-control" required>

                                    </div>

                                   

                                    <div class="form-group mb-3">

                                        <label for="account-name" class="form-control-label">Account Name</label>

                                        <input type="text" name="account-name" id="account-name" disabled="disabled" v-model="newBill.account_name" class="form-control" required>

                                    </div>


                                   
                                    
                                    <div class="input-group mb-3" v-if="resolveAccountNumberError">
                                        <p class="text-danger"><b>NOTE : </b> {{resolveAccountNumberError}}</p>
                                    </div>
                                </div>
                        
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success" v-if="isAccountResolved">Save</button>
                                    <button @click.prevent="resolveAccountNumber" class="btn btn-primary" v-else> <i :class="spinClass"></i>Verify Account</button>
                                </div>
                            </form>
                    
                        </div>
                        <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            

            <div class="modal fade" id="updatebill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

                <div class="modal-dialog modal-sm" role="document">

                        <div class="modal-content">

                            <div class="modal-header">

                                <h4 class="modal-title">Update A bill</h4><br>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>

                            </div>
                    
                            <form method="post" @submit.prevent="updateBill">
                        
                                <div class="modal-body">

                                    <div class="form-group mb-3">

                                        <label for="bill-name" class="form-control-label">Bill Name</label>
                                        <input type="text" name="bill-name" id="bill-name" v-model="currentBill.name" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="bill-amount" class="form-control-label">Bill Amount</label>
                                        <input type="text" name="bill-amount" id="bill-amount" v-model="currentBill.amount" class="form-control" required>
                                    </div>
                                                                        
                                    <div class="form-group mb-3" v-if="isAdmin">

                                        <label for="bill-occurs" class="form-control-label">How often Bill should Occur</label>

                                        <select name="bill-occurs" id="bill-occurs" v-model="currentBill.occurs" class="form-control" required>

                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                            <!-- <option value="yearly">Yearly</option> -->

                                        </select>

                                    </div>

                                     <div class="form-group mb-3">

                                        <label for="bill-category" class="form-control-label">Bill Category</label>

                                        <select name="bill-category" id="bill-category" v-model="currentBill.bill_category_id" class="form-control" required>

                                            <option  value="null">No Category Available</option>
                                            <template v-if="billcategories.length > 0">
                                                <option v-for="(category, index) in billcategories" :key="index" :value="category.id">{{category.name}}</option>
                                            </template>

                                        </select>

                                    </div>

                                      <div class="form-group mb-3" v-if="isAdmin">

                                        <label for="bill-frequency" class="form-control-label">Automate Bill</label>

                                        <select name="bill-frequency" id="bill-frequency" v-model="currentBill.frequency" :disabled="!currentBill.recipient_code"  class="form-control" required>

                                            <option value="once">No</option>
                                            <option value="always">Yes</option>

                                        </select>

                                        <span class="text-danger" v-if="!currentBill.recipient_code"> Please create recipient code for bill before automating </span>
                                        
                                    </div>

                                    <div class="form-group mb-3">

                                        <label for="bill-status" class="form-control-label">Status</label>

                                        <select name="bill-status" id="bill-status" v-model="currentBill.status" class="form-control" required>

                                            <option value="0">Deactivate</option>
                                            <option value="1">Activate</option>
                                         
                                        </select>

                                    </div>


                                    <div class="form-group mb-3">

                                        <label for="bill-frequency" class="form-control-label">Bank</label>

                                        <select name="bill-frequency" id="bill-frequency" @change="isAccountResolved = false" v-model="currentBill.bank_code" class="form-control" required>
                                            <option value="">Choose a Bank </option>
                                            <option  v-for="bank in banks" :value="bank.code">{{bank.name}}</option>

                                            <!-- <option :value="code" v-for="(code,index) in Object.keys(banks)" :key="index">{{banks[code]}}</option> -->
                                            

                                        </select>
                                        
                                    </div>

                                     <div class="form-group mb-3">

                                        <label for="account-number" class="form-control-label">Account Number</label>

                                        <input type="number" name="account-number" id="account-number" @change="isAccountResolved = false "  v-model="currentBill.account_number" class="form-control" required>

                                    </div>

                                    <div class="form-group mb-3">

                                        <label for="account-name" class="form-control-label">Account Name</label>

                                        <input type="text" name="account-name" id="account-name" disabled="disabled" v-model="newBill.account_name" class="form-control" required>

                                    </div>

                                     <div class="input-group mb-3" v-if="resolveAccountNumberError">
                                        <p class="text-danger"><b>NOTE : </b> {{resolveAccountNumberError}}</p>
                                    </div>
                                    
                                    <!-- <div class="input-group mb-3">
                                        <p class="text-danger"><b>NOTE : </b> {{currentBill.name}} will charge {{formatAsCurrency(parseFloat(currentBill.amount))}}  {{currentBill.occurs}} {{currentBill.frequency}}</p>
                                    </div> -->
                                </div>
                        
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                     <button type="submit" class="btn btn-success" v-if="isAccountResolved">Save</button>
                                    <button @click.prevent="resolveAccountNumber" class="btn btn-primary" v-else> <i :class="spinClass"></i>Verify Account</button>
                                </div>
                            </form>
                    
                        </div>
                        <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

        </div>

       
    </div>
</template>
<script>

import { utilitiesMixin } from './../../mixins';

import BillCategoryManager from './BillCategoryManager.vue';


export default {

    mixins: [utilitiesMixin],

    components: {
        'categoryManager': BillCategoryManager
    },

    props : {
        isAdmin : {
            type : Boolean,
            default : false
        }
    },

    data(){

        return {

            screenView : 'list',

            currentBill : {

            },

            loading : false,

            banks : [],

            search : '',

            billTransactions : [],

            isFetchingTransactions: false,

            isCreatingRecipient :false,
            
            viewableBills : [],

            isAccountResolved : false,

            resolveAccountNumberError : '',

            store : [],

            billcategories : [],

            newBill : {
                
                name : '',
                amount : '',
                frequency : 'once',
                occurs : 'monthly',
                bank_code : '',
                account_number : '',
                account_name : '',
                bill_category_id : ''
            }

        }
    },
    async created(){

       this.RefreshBills();
       this.RefreshBillCategories();
        this.getBanks();
    },

    beforeUpdate : function(){
     if ( $.fn.DataTable.isDataTable('#order-listing') ) {
             $('#order-listing').DataTable().destroy()
     }
    },
    updated() {
            //this.initDataTable();
    },

    watch: {

        search : {
            handler: _.debounce(function(){
                this.searchBills();
            },0)
        },
    },

    computed : {

        bills: {

            get(){

                return this.viewableBills;
            },
            set(newValue){
                
                this.viewableBills = newValue;
            }
        },

        activeBills(){

            return this.$store.state.bills.filter((bill) => bill.status == 1);
        }

    },


    methods : {

        initDataTable() {
      
            $("#order-listing").DataTable({
                aLengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
                dom: "Bfrtip",
                buttons: ["copy", "csv", "excel", "pdf"],
            
                iDisplayLength: 5,
                sPaginationType: "full_numbers",
                language: {
                search: ""
                }
            });
        },

        searchBills(){

            this.bills = this.$store.state.bills.filter((bill)=>bill.name.indexOf(this.search) != -1);

        },

        async resolveAccountNumber() {
        
            this.startResolving();

            if (this.screenView == 'single') {
                var number = this.currentBill.account_number;
                var code = this.currentBill.bank_code;
            } else {
                var number = this.newBill.account_number;
                var code = this.newBill.bank_code;
            }
            
            const req = {bank_code : code, account_number : number};

            await axios.get('/resolve/account/number', { params : req}).then((res)=> {
                let data  = res.data;
                this.newBill.account_name = data.data.account_name;

                this.isAccountResolved = true;
               
            }).catch((err)=> {

                this.resolveAccountNumberError = err.response.data;
            })

            this.stopResolving();

            
        },

        startResolving() {

            this.spinClass['fa-spinner'] = true;
            this.spinClass['fa-spin'] = true;
            this.spinClass['fa-check-circle-o'] = false;
            this.resolveAccountNumberError = '';
            this.newBill.account_name = '';
        },

        stopResolving() {
            this.spinClass['fa-spinner'] = false;
            this.spinClass['fa-spin'] = false;
            this.spinClass['fa-check-circle-o'] = true;
        },
        async createNewBill(){

            $('#newbill .close').click();

            this.loading = true;

            var bill = this.newBill;

            await axios.post(`/ucnull/bills/store`,bill).then((res)=> {

                this.alertSuccess('New Bill Created');

                //refresh bills
                this.RefreshBills();

              
            }).catch((e)=> {

                this.alertError(e.response.data);
            })

             $('.modal-backdrop').remove();

             this.loading = false;
        },
       
        async updateBill(){

            $('#updatebill .close').click();

            this.loading = true;

            var bill = this.currentBill;

            await axios.put(`/ucnull/bills/update/${bill.id}`,bill).then((res)=> {

                this.alertSuccess('Bill was updated');

                //refresh bills
               this.RefreshBills();

            }).catch((e)=> {

                this.alertError(e.response.data);
            })

             $('.modal-backdrop').remove();

            this.loading = false;

            this.screenView = 'list'

        },


        async payBill() {

            this.loading = true;

            var bill = this.currentBill;

            await axios.post(`/ucnull/bills/single/pay/${bill.id}`).then((res)=> {

                this.alertSuccess('Bill Payment was successful');
            }).catch((e) => {

                this.alertError(e.response.data);
            })

            this.loading = false;
        },

        async requestPayment() {

            this.loading = true;

            var bill = this.currentBill;

            await axios.post(`/ucnull/bills/request/payment/${bill.id}`).then((res)=> {

                this.alertSuccess('Payment request has been sent');
                location.reload();
                
            }).catch((e) => {

                this.alertError(e.response.data);
            })

            this.loading = false;
        },

        async deleteBill(){

            this.loading = true;

            var bill = this.currentBill;

            await axios.delete(`/ucnull/bills/delete/${bill.id}`).then((res)=> {

                this.alertSuccess('Bill was deleted');

                //refresh bills
               this.RefreshBills();

            }).catch((e)=> {

                this.alertError(e.response.data);
            })


             this.loading = false;

            this.screenView = 'list'

            this.currentBill = {}
        },

        async getTransactions(){

            this.isFetchingTransactions = true;

            var bill = this.currentBill;

            await axios.get(`/ucnull/bills/transactions/${bill.id}`).then((res)=> {

                this.billTransactions = res.data;

            }).catch((e)=> {

                this.alertError(e.response.data);
            })

             this.isFetchingTransactions = false;
        },
        async payAllBills(){

            this.startLoading();

            await axios.post(`/ucnull/bills/pay/active/bills`).then((res)=> {

                this.alertSuccess(res.data);
                
            }).catch((e)=>{

                this.alertError(e.response.data);
            })

            this.stopLoading();
        },

        async createRecipient(){

            this.isCreatingRecipient = true;

            await axios.post(`/ucnull/transfer-controls/create/recipient/${this.currentBill.bank_id}`).then((res)=>{

                this.alertSuccess(res.data);

                this.RefreshBills();

            }).catch((e)=> {

                this.alertError(e.response.data);
            })

             this.isCreatingRecipient = false;
        },

        async getBanks(){

            await axios.get('/ucnull/bills/banks').then((res)=>{

                this.banks = res.data;
                console.log(res.data[0]['code'] + '  '+ res.data[0]['name'])

            }).catch((e)=>{

                this.handleApiErrors(e);
            })
        },

        viewBill(e) {
           
            var index = e.currentTarget.getAttribute('data-index');

            this.currentBill  = this.bills[index];

            this.screenView  = 'single';

            this.isAccountResolved = true;

        },

        viewList()
        {
            this.currentBill = '';

            this.billTransactions = [];

            this.screenView = 'list';

            this.isAccountResolved = false;
        },

        async RefreshBills(){

            await this.$store.dispatch('setBills');

            this.bills = this.$store.state.bills;
        },

        async RefreshBillCategories(){

            await this.$store.dispatch('setBillCategories');

            this.billcategories = this.$store.state.billcategories;
        }
    }
}
</script>

<style scoped>
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

tbody tr {
    cursor : pointer;
}


#transactions-table  tr td , #transactions-table tr th{

    font-size: 12px;
}

</style>