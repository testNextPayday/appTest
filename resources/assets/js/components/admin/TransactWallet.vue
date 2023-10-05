<template>

        <div>
           
            <div class="alert alert-info" v-if="wait"><i :class="spinClass"></i> Please wait</div>

            <form @submit.prevent="storeTransaction" class="row" >

                <div class="form-group col-md-6">
                    <label class="form-control-label">Amount</label>
                    <input type="number" step="0.001" class="form-control" v-model="upload.amount">
                </div>

                <div class="form-group col-md-3">
                    <label class="form-control-label">Collection Method</label>
                    <select class="form-control" v-model="upload.method">
                        <option value="Cash">Cash</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Deposit">Deposit</option>
                        <option value="DDAS">DDAS</option>
                        <option value="Transfer">Transfer</option>
                        <option value="Remita">Remita</option>
                        <option value="Paystack">Paystack</option>
                        <option value="Set-off">Set-off</option>
                        <option value="RavePay">RavePay</option>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label class="form-control-label">Debit or Credit</label>
                    <select name="status" class="form-control" v-model="upload.direction">
                        <option value="2">Debit</option>
                        <option value="1">Credit</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label class="form-control-label">Describe Transaction</label>
                    <input type="text" v-model="upload.desc" class="form-control">
                </div>

                <div class="form-group col-md-3">
                    <label class="form-control-label">Transaction Date</label>
                    <input type="date" v-model="upload.date" class="form-control" name="trnx_date">
                </div>

                <div class="form-group col-md-3">
                    
                    <button class="btn btn-sm btn-primary" style="margin-top:25px;"> <i :class="spinClass"></i> Update</button>
                </div>


            </form>
            <div>
                <table class="table table-responsive" style="height:400px;overflow:scroll" id="transactions">
                   <thead>
                       <tr>
                           <th>S/N</th>
                           <th>Action</th>
                           <th>Description</th>
                           <th>Date</th>
                           <th>Collection Method</th>
                           <th>Dr.</th>
                           <th>Cr.</th>
                       </tr>
                   </thead>
                    <tbody>
                        <tr v-for="(trnx, index) in transactions" :key="index">
                            <td>{{parseInt(index) + 1}}</td>
                            <td>
                                <button class="btn " @click="deleteTransaction(trnx.id)"> Delete</button>
                                <button type="button"  class="btn btn-sm btn-warning" data-toggle="modal" :data-target="`#exampleModal${trnx.id}`"> Edit</button>
                            </td>
                            <td>{{trnx.description}}</td>
                            <td>{{trnx.collection_date}}</td>
                             <td>{{trnx.collection_method}}</td>
                            <td>₦{{trnx.direction == 2 ? formatAsCurrency(trnx.amount) : '0.00'}}</td>
                            <td>₦{{trnx.direction == 1 ? formatAsCurrency(trnx.amount) : '0.00'}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>


            
            <!-- Modal for editting by James -->

            <!-- Modal -->
<div v-for="postTrnx in transactions" 
 class="modal fade" :id="`exampleModal${postTrnx.id}`" tabindex="-1" role="dialog"
  aria-labelledby="exampleModalLabel" aria-hidden="true">

    <div class="modal-dialog border border-dark" role="document">
      <div class="modal-content">
        <div class="modal-header bg-dark">
          <h5 class="modal-title text-warning" id="exampleModalLabel">Edit Wallet Transaction</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span class="text-danger" aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
           
            <form @submit.prevent="updateTransaction(this.id = postTrnx.id)" class="row" >
                <div class="form-group col-md-3">
                    <label class="form-control-label">Amount</label>
                    <input type="number" step="0.001" class="form-control"  v-model="amount">
                    <!-- <input type="hidden" step="0.001" class="form-control"  v-model="this.id = postTrnx.id"> -->
                </div>

                <div class="form-group col-md-3">
                    <label class="form-control-label">Collection Method</label>
                    <select class="form-control" v-model="method">
                        <option value="Cash">Cash</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Deposit">Deposit</option>
                        <option value="DDAS">DDAS</option>
                        <option value="Transfer">Transfer</option>
                        <option value="Remita">Remita</option>
                        <option value="Paystack">Paystack</option>
                        <option value="Set-off">Set-off</option>
                        <option value="RavePay">RavePay</option>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label class="form-control-label">Debit or Credit</label>
                    <select name="status" class="form-control" v-model="direction">
                        <option value="2">Debit</option>
                        <option value="1">Credit</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label class="form-control-label">Describe Transaction</label>
                    <input type="text" v-model="desc" class="form-control">
                </div>

                <!-- <div class="form-group col-md-3">
                    <label class="form-control-label">Transaction Date</label>
                    <input type="date" v-model="date" class="form-control" name="trnx_date">
                </div> -->

                <div class="form-group col-md-3">
                    <button class="btn btn-sm btn-primary" style="margin-top:25px;"> <i :class="spinClass"></i> Update</button>
                </div>
            </form>
 
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

        </div>
</template>

<script>
    import { utilitiesMixin } from './../../mixins';
    export default  {
         mixins: [utilitiesMixin],
        props : ['reference', 'user'],

        data(){
            return {
                upload : {
                    amount : '',
                    direction : '',
                    date: '',
                    desc : '',
                    method : 'DDAS'
                },
                transactions : [],
                wait : false,

                amount : null,
                id : null,
                direction : '',
                    date: '',
                    desc : '',
                    method : 'DDAS'

            };
        },

        computed : {

            
        },

        mounted() {
            
            this.getWalletTransactions();
            
        },
        beforeUpdate : function(){
            if ( $.fn.DataTable.isDataTable('#transactions') ) {
                    $('#transactions').DataTable().destroy()
            }
        },
        updated() {
                this.initDataTable();
        },

        methods : {

            initDataTable() {
      
                $("#transactions").DataTable({
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

            async deleteTransaction(id){
                let trnxId = id;
                if (this.wait) { return false;}
                this.wait = true;
                await axios.post(`/ucnull/loan-wallet-transactions/delete/${trnxId}`).then((res)=> {
                    this.alertSuccess('Successfully deleted ');
                })
                this.wait = false;
                this.getWalletTransactions();
            },

            async getWalletTransactions() {
                if (this.wait) { return false;}
                await axios.get(`/ucnull/loan-wallet-transactions/user/${this.user}`).then((res)=> {
                   this.transactions =  res.data
                })
            },

            async storeTransaction(){
                if (this.wait) { return false;}
                this.startLoading();
                const req = this.upload;
                await axios.post(`/ucnull/loan-wallet-transactions/store/${this.reference}`, req).then((res)=> {
                   this.alertSuccess('Successfully saved ');
                   this.getWalletTransactions();
                })
                this.stopLoading();
            },

            async updateTransaction(id){
                
                // alert(e)
                if (this.wait) { return false;}
                this.startLoading();
                this.amount
                
                await axios.post(`/ucnull/loan-wallet-transactions/update`, {
                    amount : this.amount,
                    id : id,
                    direction : this.direction,
                    // date: '',
                    desc : this.desc,
                    method : this.method
                }).then((res)=> {
                    // alert(res)
                   this.alertSuccess('Successfully saved ');
                   this.getWalletTransactions();
                })
                this.stopLoading();
            },

           
        },

    }
</script>