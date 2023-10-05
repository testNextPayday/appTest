<template>
    <div>
        <div class="row">
            <div class="col-sm-12" v-if="bankDetails.length == 0">
                <p>
                    <button class="btn btn-sm btn-primary pull-right" @click="switchMode()" v-if="!mode" :disabled="loading">
                        <i class="fa fa-plus"></i>&nbsp;Add New
                    </button>
                    <button class="btn btn-sm btn-danger pull-right" @click="switchMode()" v-else :disabled="loading">
                        <i class="fa fa-close"></i>&nbsp;Cancel
                    </button>
                </p>
                <br/>
            </div>
        </div>
        
        <div class="row" v-if="!mode">
            <div class="col-sm-6 col-md-6" v-for="bankDetail in bankDetails">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-briefcase"></i>{{bankDetail.bank_name}}
                        <span class="pull-right">
                            <a href="#" @click="deleteBankDetail(bankDetail.id)">
                                <i class="fa fa-trash text-danger"></i>
                            </a>
                        </span>
                        <span class="clearfix"></span>
                    </div>
                    <div class="card-body">
                        <h4><small>Account Number:</small> <strong>{{bankDetail.account_number}}</strong></h4>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6">
          <!--       <form method="POST" accept="#">
                  <div class="form-group mx-sm-2 ">
                    <label for="bvn">Bvn Number</label>
                    <input type="number" class="form-control" id="bvn" placeholder="Bvn Number" maxlength="11" required>
                  </div>
                  <button type="submit" class="btn btn-success float-right">Update Bvn</button>
                </form>   -->
            </div>
            <!--/.col-->
        </div>
        
        <div class="row justify-content-center" v-else>
          
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <strong>New</strong>
                        <small>Bank Detail</small>
                    </div>
                        
                    <div class="card-body">
                        <form method="POST" action="#" @submit.prevent="saveBankDetail">
                            <p class="text-center text-danger">{{error_message}}</p>
                            
                            <div class="form-group">
                                <label class="control-label" for="bank_name"><strong>Bank Name</strong></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-addon"><i class="icon-layers"></i></span>
                                    <select name="bank_name" id="bank_name"
                                        class="form-control" required v-model="bankDetail.bank_code">
                                        <option v-for="bankCode in bankCodes" :value="bankCode">{{banks[bankCode]}}</option>                                     
                                    </select>
                                </div>    
                            </div>
                                
                            <div class="form-group">
                                <label class="control-label" for="department"><strong>Account Number</strong></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-addon"><i class="icon-briefcase"></i></span>
                                    <input type="text" name="account_number" id="account_number"
                                        class="form-control" required v-model="bankDetail.account_number"
                                        placeholder="Your Account Number" title="Your Account Number" pattern="\d*" maxlength="10">
                                </div>    
                            </div>
                                
                            <button type="submit" class="btn btn-success btn-block" :disabled="loading">
                                <i :class="buttonClass"></i>
                                Save
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['userBankDetails', 'banks'],
        
        data() {
            return {
                bankDetail: {
                    bank_name: '',
                    account_number: '',
                    bank_code: ''
                },
                bankDetails: [],
                mode: false,
                loading: false,
                error_message: '',
                buttonClass: {
                    fa: true,
                    "fa-check-circle-o": true,
                    "fa-spin": false,
                    "fa-spinner": false
                },
                bankCodes: [],
            };
        },
        
        mounted() {
            this.initializeBankData();  
        },
        
        methods: {
            initializeBankData() {
                this.userBankDetails.forEach(bankDetail => {
                    this.bankDetails.push(bankDetail);  
                });
                this.bankCodes = Object.keys(this.banks);
            },
            
            saveBankDetail(event) {
                let valid = this.validateData();
                if(this.bankDetail.bankCode == '') {
                    alert('Please select a bank');
                    return;
                }
                if(this.bankDetail.bank_code == '') {
                    alert('Please select a bank');
                    return;
                }
                
                if(this.bankDetail.account_number == '' || this.bankDetail.account_number.length != 10) {
                    alert('Please enter a valid account number');
                    return;
                }
                
                if(valid) {
                    this.startLoading();
                    this.bankDetail.bank_name = this.banks[this.bankDetail.bank_code];
                    axios.post(`/profile/bank/add`, this.bankDetail).then((response) => {
                        if(response.data.status === 1) {
                            this.bankDetails.push(response.data.bankDetail);
                            this.mode = !this.mode;
                        } else {
                            this.setError('An error occurred. Please try again');
                        }
                        this.stopLoading();
                    }).catch((error) => {
                        this.setError(error.message);
                        this.stopLoading();
                    });
                }
            },
            
            deleteBankDetail(id) {
                let proceed = confirm('Are your sure?');
                if(!proceed) {
                    return;
                }
                this.startLoading();
                axios.get(`/profile/bank/delete/${id}`).then((response) => {
                    if(response.data.status === 1) {
                        let bank = this.bankDetails.find((bank) => bank.id === id);
                        let index = this.bankDetails.indexOf(bank);
                        if(index !== -1) {
                            this.bankDetails.splice(index, 1);
                        }
                    } else {
                        this.setError(response.data.message);
                    }
                    this.stopLoading();
                }).catch((error) => {
                    this.setError(error.message);
                    this.stopLoading();
                });
            },
            
            validateData() {
                return true;
            },
            
            switchMode() {
                this.mode = !this.mode;
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
            }
        }
    };
</script>