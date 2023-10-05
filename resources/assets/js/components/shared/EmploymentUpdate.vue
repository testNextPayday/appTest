<template>
    <span>
        <button class="btn btn-primary btn-xs" @click="showModal = true">Update</button>
        <modal v-if="showModal" @close="closeModal">
            <div slot="header">
                <h4>Update Employment
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>
    
            <div slot="body">
                <form method="POST" :action="url" id="updateEmploymentForm" enctype="multipart/form-data" width="70%">
                    <p class="text-center text-danger">{{error_message}}</p>
                    <input type="hidden" name="_token" :value="csrfToken">
                    <div class="form-group row">
                        <label class="control-label col-md-6" for="work_id_card"><strong>Work ID</strong></label>
                        <input type="file" class="form-control col-md-6" id="work_id_card" name="work_id_card">
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-6" for="employment_letter"><strong>Employment Letter</strong></label>
                        <input type="file" class="form-control col-md-6" id="employment_letter" name="employment_letter">
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-6" for="confirmation_letter"><strong>Confirmation Letter</strong></label>
                        <input type="file" class="form-control col-md-6" id="confirmation_letter" name="confirmation_letter">
                    </div>
                   
                   <!-- This division is restricted to admin updates only -->
                  <div v-if="isAdmin">
                     <div class="form-group row" >
                        <label class="control-label col-md-6" for="employer_id"><strong>Employer </strong></label>
                        <select type="text" class="form-control col-md-6" id="employer_id" name="employer_id" :value="employment.employer_id">
                            <option v-for="employer in employers" :value="employer.id" :key="employer.id">{{employer.name}}</option>
                        </select>
                    </div>


                    <div class="form-group row" >
                        <label class="control-label col-md-6" for="mda"><strong>MDA</strong></label>
                        <input type="text" class="form-control col-md-6" id="mda" name="mda" :value="employment.mda">
                    </div>

                     <div class="form-group row" >
                        <label class="control-label col-md-6" for="netpay"><strong>Net Salary</strong></label>
                        <input type="text" class="form-control col-md-6" id="netpay" name="net_pay" :value="employment.net_pay">
                    </div>

                     <!--<div class="form-group row" >
                        <label class="control-label col-md-6" for="salary"><strong>salary</strong></label>
                        <input type="text" class="form-control col-md-6" id="salary" name="monthly_salary" :value="employment.monthly_salary">
                    </div>-->
                     <div class="form-group row" >
                        <label class="control-label col-md-6" for="payroll_id"><strong>Payroll ID</strong></label>
                        <input type="text" class="form-control col-md-6" id="payroll_id" name="payroll_id" :value="employment.payroll_id">
                    </div>
                    
                   
                  </div>
                  
                   
                   
                </form>    
            </div>
    
            <div slot="footer">
                <div>
                    <button type="submit" form="updateEmploymentForm" class="btn btn-success pull-right">
                        <i :class="buttonClass"></i>
                        Update Employment
                    </button>
                </div>
            </div>
        </modal>    
    </span>
    
    
</template>
<script>
    
    
    export default {
        props: ["employment", "url", "csrfToken","isAdmin","employers"],
        
        data() {
            return {
                loading: false,
                error_message: '',
                buttonClass: {
                    fa: true,
                    "fa-check-circle-o": true,
                    "fa-spin": false,
                    "fa-spinner": false
                },  
                showModal: false,
            };
        },
        
        methods: {
            closeModal() {
                this.showModal = false;
            },
            
            startLoading() {
                this.loading = true;
                this.buttonClass['fa-spinner'] = true;
                this.buttonClass['fa-spin'] = true;
                this.buttonClass['fa-check-circle-o'] = false;
            },
            
            stopLoading() {
                this.loading = false;
                this.buttonClass['fa-spinner'] = false;
                this.buttonClass['fa-spin'] = false;
                this.buttonClass['fa-check-circle-o'] = true;
            },
            
            setError(message) {
                this.error_message = message;
            },
            
            clearError() {
                this.error_message = '';  
            },
            
            validateEmail(email) {
                return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email) ? true: false;
            },
        },
    };
</script>

<style>
    .card .card-body {
        padding-top :0px;
    }
</style>
