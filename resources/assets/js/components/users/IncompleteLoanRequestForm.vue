<template>
    <div>
         <div class="alert alert-info"> 
            <span> You have an uncompleted loan request with us . Would you love to continue ?</span> 
            <a href="#oldRequest" class="badge badge-primary" data-toggle="modal" data-target="#oldRequest">View details</a>
        </div>

        <div class="modal fade" id="oldRequest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Uncompleted Loan Request</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5 class="alert alert-danger"> <small>If error persists contact support for help</small></h5>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Request Date
                                <span class="badge badge-primary badge-pill">{{request.created_at}}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Loan Amount 
                                <span class="badge badge-primary badge-pill">{{data.amount}} @ {{data.interest_percentage}}%</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                            Duration
                                <span class="badge badge-primary badge-pill">{{data.duration}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Statement Link
                                <span class="badge  badge-pill"><a :href="data.bank_statement" target="_blank">Statement PDF</a></span>
                            </li>

                             <li class="list-group-item d-flex justify-content-between align-items-center">
                                Payment Status (Trnx reference)
                                <span class="badge badge-primary badge-pill"> {{request.paid_status ? 'Paid' : 'Not Paid'}} ({{request.trnx_reference}})</span>
                            </li>

                            
                        </ul>

                        <form @submit.prevent="submitRequest">
                            <button type="submit" class="btn btn-primary"> <i :class="spinClass"></i> Submit Request</button>
                        </form>
                    </div>
                
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</template>

<script>
 import { utilitiesMixin } from './../../mixins';
export default {
    props: ['request'],
    mixins: [utilitiesMixin],
    data() { 
        return{
            data : {}
        };
    },
    mounted() {
        this.data = JSON.parse(this.request.data);
        this.data.charge = this.data.success_fee;
        this.data.reference = this.request.trnx_reference;
        this.data.upfront_status = this.data.upfront_interest;
        
    },
    methods: {
        submitRequest() {
            this.startLoading();
            var data = this.data;
            axios.post(`/loan-requests/store`, data).then(res => {

                swal({
                title: "Good job!",
                text: "You have successfully booked a loan with Nextpayday.",
                icon: "success",
                button: "OK",
                }).then(res => {                  
                    window.location.reload();
                });                    
            }).catch(err => {
                               
                swal("Error occured!", `${err.response.data}`, "error");
            });
            this.stopLoading();
        }
    }
}
</script>