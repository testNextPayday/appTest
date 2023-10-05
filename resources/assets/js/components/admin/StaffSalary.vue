<template>

    <div>



        <div v-if="loading">
            <newton-loader></newton-loader>
        </div>

        <div v-else>
           
            <div class="row">

                

                <div class="col-md-12">

                    <div class="card">

                        <div class="card-header row">
                            
                            

                            <div class="col-md-6 grid-margin offset-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-0">{{staffs.length}} Staff(s) to be Paid</h4>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-inline-block pt-3">
                                                <div class="d-flex">
                                                    <h2 class="mb-0">₦{{formatAsCurrency(sumSalaries)}}</h2>
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
    
                          

                        </div>

                        <div class="card-body">

                            <table class="table table-responsive-sm table-hover table-outline mb-0" >

                                <thead class="thead-light">

                                    <tr>
                                       
                                      
                                        <th>Name</th>
                                        <th>Salary</th>
                                        <th>Account No</th>
                                        <th>Bank</th>
                                        <th>Recipient Code</th>
                                        <th>Actions</th>
                                        
                                    </tr>

                                </thead>

                                <tbody v-if="staffs.length > 0">
                                  
                                    <staff-template @staffupdate="refreshStaffs" v-for="(staff,index) in staffs" :key="index" :staff="staff" :showMore="true"></staff-template>
                                  
                                </tbody>

                                <tbody v-else>

                                    <tr>
                                        <td colspan="3" class="text-center"> No staffs available</td>
                                    </tr>

                                </tbody>

                            </table>
                        </div>

                        <div class="card-footer row">

                            <div class="col-md-6">

                                <check-balance></check-balance>

                            </div>

                            <div class="col-md-6">

                                <form @submit.prevent="paySalaries" class="pull-right" v-if="staffs.length">

                                    <button class="btn btn-danger btn-xs">Pay All ({{staffs.length}}) Staffs</button>

                                </form>
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

import staffTemplate from './staffTemplate';

import checkBalance from './checkBalance';

export default {

    mixins: [utilitiesMixin],

    components  : {

        'staff-template' : staffTemplate,
        
        'check-balance' : checkBalance
    },


    data(){

        return {

           
            loading : false,
            staffs : []

        }
    },
    created(){

        this.refreshStaffs();
    },

    watch: {

    },

    computed : {

        sumSalaries(){

            var amount = 0;

            this.staffs.forEach((obj)=> amount += obj.salary);

            return amount;
        }

    },


    methods : {

         async paySalaries(){

            this.startLoading();

            let bankList  = this.staffs.map((a)=> a.banks[a.banks.length - 1].id);
        
            const payload = {banks : bankList};

            await axios.post(`/ucnull/payment-salaries/pay/staffs/bulk`,payload).then((res)=>{

                this.alertSuccess(res.data);

            }).catch((e)=> {

                this.alertError(e.response.data);
            })

            this.stopLoading();
        },

        async refreshStaffs(){

            await axios.get('/ucnull/payment-salaries/staffs').then((res)=>{

                this.staffs = res.data;

            }).catch((e)=>{

                this.alertError('Oops an error occred');
            });
        }

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

.table td, .table th {

    padding: 10px;
}


#staffs-table  tr td , #staffs-table tr th{

    font-size: 12px;
}

</style>