<template>
                    <div class="row">
                    <div class="col-12">
                <!-- <button @click.prevent="AddField" class="btn btn-primary">Add More</button> -->
                    <form  method="post"  @submit.prevent="submitRepayments">
                        
                        <table id="repayments-table" class="table">
                            <thead>
                                <tr>
                                   
                                    <th >Borrower</th>
                                    <th>Amount Paid</th>
                                    <th>Method</th>
                                    <th>Collection Date</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(field,index) in fields">
                                    <td > 
                                       
                                        <input type="text" name="user_query" v-model="query" class="form-control" placeholder="Search Borrower Name">
                                        <select class="form-control" name="reference" v-model="field.borrower" placeholder="Please Select Name" >
                                            <template v-if="users.length > 0"><option v-for="user in users" :key="user.id" :value="user.id">{{user.name}}</option></template>

                                            <template v-else>
                                            <option selected="selected">No User found</option>
                                            </template>
                                        </select>
                                            
                                    </td>
                                  
                                    <td>
                                    
                                        <input type="text" name="paid_amount" class="form-control" v-model="field.paid_amount" required>
                                    </td>
                                    <td>
                                         <select class="form-control" style="width:auto;" v-model="field.payment_method">
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
                                    </td>
                                   
                                    
                                   
                                    <td><input type="date" v-model="field.collection_date" placeholder="Collection date" name="collection_date[]" class="form-control"/></td>
                                    <!--<td><input type="file" @change="getFile($event,index)" class="form-control"/></td>-->
                                    <td><input type="text" v-model="field.remarks" class="form-control"/></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary" style="float:right">Submit</button>
                        </form>
                       
                    </div>
                </div>
    
</template>
<script>
    import { utilitiesMixin } from '../mixins';
    import {mapGetters} from 'vuex';
    export default {
         props: ["url"],
        
        mixins: [utilitiesMixin],
        data(){ 
            return{
                options:[],
                childrenOptions:[],
                fields: [{
                    borrower: '',
                    amount: '',
                    payment_method: '',
                    collection_date: '',
                    remarks: ''
                }],
                query : ''
        }},
        created(){
          axios.get(this.url + '/borrowers').then((resp) => {
              this.options  = resp.data.map(i => ({
                 id : i.reference,
                 label : i.user.name,
                 children: i.repayment_plans.map(u => ({
                     id : u.id,
                     label :u.month_no + ' ' + i.user.name + ' '+u.emi
                 }))
              }));
          })  
         
        },
        methods: {
            AddField: function() {
                this.fields.push({
                borrower: '',
                payment_method: '',
                collection_date: '',
                remarks: ''

            });
            },
            showRepayments(i){
                
            },
            getFile(e,index){
                
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0],index);
                
            },
            createImage(file,index) {
                let reader = new FileReader();
                let vm = this;
                console.log(index);
                reader.onload = (e) => {
                    this.fields[index].payment_proof = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            submitRepayments(){
                axios.post(this.url, this.fields).then((resp) => {
                    this.alertSuccess(resp.data.success);
                })
            },
            searchBorrowers() {
      
                let userQuery = this.query;
                this.$store.dispatch('searchRepaymentBorrowers', {
                    userQuery
                })

            }
        },
        watch : {
            query : {
                handler: _.debounce(function(){
                    this.searchBorrowers();
                },0)
            }
        },
        computed : {
            users() {
                return this.RepaymentBorrowers;
            },
            ...mapGetters(['RepaymentBorrowers'])
        }
    }
</script>
<style type="text/css">
    .form-control{
        width:auto !important;
    }
</style>