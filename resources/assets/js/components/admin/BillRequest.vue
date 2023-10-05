<template>

    <div>

        <div v-if="loading">
            <newton-loader></newton-loader>
        </div>

        <div v-else>
           
            <div class="row">

                <div class="col-md-12" >

                    <div class="card" style="height:350px; overflow-y:scroll;">

                        <div class="card-header">
                            Pending Bills
                        </div>

                        <div class="card-body">

                            <table id = "order-listing" class="table table-responsive-sm table-hover table-outline mb-0" >

                                <thead class="thead-light">

                                    <tr>
                                        <th>S/N</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>

                                </thead>

                                <tbody v-if="pendingbills.length > 0" >
                                  
                                   
                                    <tr v-for="(bill,index) in pendingbills" :key="index"  :data-index="index"  class="text-success">

                                       <td>{{index + 1}}</td>
                                        <td>
                                            <span>{{bill.status}}</span>
                                        </td>
                                         <td>
                                            {{bill.date}}
                                        </td>
                                        <td>
                                            {{bill.name}}
                                        </td>
                                        <td>
                                            {{formatAsCurrency(parseFloat(bill.amount))}}
                                        </td>

                                        <td>
                                            <button class="btn btn-warning btn-sm" @click="payBill(bill.bill_id)"> <i class="fa fa-credit-card"></i> Pay Bill</button>
                                             <button class="btn btn-danger btn-sm" @click="rejectBillRequest(bill.id)"> <i class="fa fa-times"></i> Reject</button>
                                        </td>
                                        
                                        
                                    </tr>
                                  
                                    
                                </tbody>

                               

                                <tbody v-else>

                                    <tr>
                                        <td colspan="3" class="text-center"> No Pending Bills available</td>
                                    </tr>

                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
                <!--/.col-->

                 <div class="col-md-12" >

                    <div class="card" style="height:350px; overflow-y:scroll;">

                        <div class="card-header">
                            Declined Bills
                        </div>

                        <div class="card-body">

                            <table id = "order-listing" class="table table-responsive-sm table-hover table-outline mb-0" >

                                <thead class="thead-light">

                                    <tr>
                                        <th>S/N</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                    </tr>

                                </thead>

                                <tbody v-if="declinedbills.length > 0" >
                                  
                                   
                                    <tr v-for="(bill, index) in declinedbills" :key="index"  :data-index="index" class="text-danger" >

                                       <td>{{index + 1}}</td>
                                        <td>
                                            <span>{{bill.status}}</span>
                                        </td>
                                         <td>
                                            {{bill.date}}
                                        </td>
                                        <td>
                                            {{bill.name}}
                                        </td>
                                        <td>
                                            {{formatAsCurrency(parseFloat(bill.amount))}}
                                        </td>
                                        
                                    </tr>
                                  
                                    
                                </tbody>

                               

                                <tbody v-else>

                                    <tr>
                                        <td colspan="3" class="text-center"> No Declined Bills available</td>
                                    </tr>

                                </tbody>

                                 

                            </table>
                        </div>
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
        isAdmin : {
            type : Boolean,
            default : false
        }
    },

    data(){

        return {
            loading : false,
            store : [],
            pendingbills : [],
            declinedbills : []
        }
    },
    async created(){

       this.getPendingBills();
       this.getDeclinedBills();
    },

    methods : {

      
        async getPendingBills(){
            await axios.get(`/ucnull/bills/pending`).then((res)=> {
                this.pendingbills = res.data;
            }).catch((e) => {

                this.alertError(e.response.data);
            })
        },

        async getDeclinedBills(){
            await axios.get(`/ucnull/bills/declined`).then((res)=> {
                 this.declinedbills = res.data;
            }).catch((e) => {
                this.alertError(e.response.data);
            })
        },

        async payBill(id) {

            this.loading = true;

            await axios.post(`/ucnull/bills/single/pay/${id}`).then((res)=> {
              
                this.alertSuccess('Bill Payment was successful');
                  location.reload();
            }).catch((e) => {

                this.alertError(e.response.data);
            })

            this.loading = false;
        },

        async rejectBillRequest(id){

            this.loading = true;

            await axios.post(`/ucnull/bills/cancel/${id}`).then((res)=> {

                this.alertSuccess('Bill Request was rejected ');
               
               location.reload();

            }).catch((e)=> {

                this.alertError(e.response.data);
            })


             this.loading = false;
        },

       

        async RefreshBills(){

            await this.$store.dispatch('setBills');

            this.bills = this.$store.state.bills;
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