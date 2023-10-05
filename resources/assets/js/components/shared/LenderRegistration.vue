<template>
    <div class="card">
        <div class="card-header">
            <strong>Investor</strong>
            <small>Verification</small>
        </div>
        
        <h5 class="text-danger text-center">
             {{errorMessage}}   
        </h5>
        <ul v-if="errorBag.length > 0">
            <li v-for="error in errorBag" class="text-danger">{{error}}</li>
        </ul>
                  
        <div id="lender-div">
            <form method="POST" @submit.prevent="submitForm" enctype="multipart/form-data">
                    
                <div class="card-body">
                    <div class="form-group">
                        <label>Account Reference </label>
                        <input type="text" class="form-control" v-model="formData.reference"  name="reference" placeholder="Enter account reference number" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="company">Select Account Type</label>
                        <select name="investor_type" v-model.number="formData.investor_type" class="form-control" required>
                            <option value="1">Single User</option>
                            <option value="2">Institution/Company</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <p><label>Upload A Valid ID (JPG, PNG)</label> <span class="pull-right"><b>Max Size: 500 KB</b></span></p>
                        <p class="text-danger"><b>{{imageErrors.licenceImage}}</b></p>
                        <p class="text-success"><b>{{imageSuccess.licenceImage}}</b></p>
                        <input type="file" id="licenceImage" name="licence" ref="licenceImage" @change="onFileChange('licenceImage')" accept="image/jpg,image/png" class="form-control" required>
                    </div>
                    
                    <br v-if="formData.investor_type == 2"/>
                    <div class="form-control" v-if="formData.investor_type == 2">
                        <p><label>Upload Registration Certificate (JPG, PNG)</label> <span class="pull-right"><b>Max Size: 500 KB</b></span></p>
                        <p class="text-danger"><b>{{imageErrors.regCertificate}}</b></p>
                        <p class="text-success"><b>{{imageSuccess.regCertificate}}</b></p>
                        <input type="file" id="regCertificate" ref="regCertificate" @change="onFileChange('regCertificate')" name="reg_certificate" class="form-control" accept="image/jpg,image/png" required>
                    </div>
                  
                    <hr>
                  
                    <div class="form-group">
                        <label>NIN Number </label>
                        <input type="text" class="form-control" v-model="formData.nin_number"  name="nin_number" placeholder="Enter NIN Number" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-warning" :disabled="loading">
                        <i :class="spinClass"></i> Submit
                    </button>
               
                </div>
            </form>
        </div>
    </div>
</template>
<script>
    import { utilitiesMixin } from './../../mixins';
    
    export default {
        mixins: [utilitiesMixin],
        
        props: ['url', 'reference'],
        
        data() {
            return {
                formData: {
                    reference: '',
                    investor_type: 1,
                    licence_type: 1,
                    licenceImage: '',
                    regCertificate: '',
                    nin_number: '',
                    managed_account: false,
                },
                imageErrors: {licenceImage: "", regCertificate: ""},
                imageSuccess: {licenceImage: "", regCertificate: ""},
            };
        },
        
        mounted() {
            if (this.reference) this.formData.reference = this.reference;  
        },
        
        methods: {
            submitForm() {
                if (!this.formData.reference) return this.alertError("Please provide a reference number");
                if (!this.formData.nin_number) return this.alertError("Please provide a nin number");
                
                if (!this.formData.licenceImage || this.formData.licenceImage.size > 500000)
                    return this.alertError("Please select a valid ID. Size limit: 500KB");
                    
                if (this.formData.investor_type == 2) {
                    if (!this.formData.regCertificate || this.formData.regCertificate.size > 500000)
                        return this.alertError("Please select a valid registration certificate. Size limit: 500KB");
                }
                    
                let data = new FormData();
                Object.keys(this.formData).forEach(key => {
                    if (!(key == 'regCertificate' && this.formData.investor_type == 1))
                        data.append(key, this.formData[key]); 
                });
                
                this.startLoading();
                
                axios.post(this.url, data, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(response => {
                    if (response.data.status === 1) {
                        this.alertSuccess(response.data.message);
                        this.initializeFormData();
                    } else {
                        this.alertError(response.data.message);
                    }
                    this.stopLoading();
                }).catch(error => {
                    if(error.response && error.response.status === 422) {
                        
                        this.errorMessage = error.response.data.message;
                        let errorMessages = Object.keys(error.response.data.errors);
                        errorMessages.forEach((errorKey) => {
                            this.errorBag.push(error.response.data.errors[errorKey][0]);
                        });
                        
                    } else {
                        this.errorMessage = error.message;
                        this.alertError(error.message);
                    }
                    
                    this.stopLoading();
                });
            },
            
            onFileChange(refName) {
                this.formData[refName] = this.$refs[refName].files[0];
                //check file size here
                if (this.formData[refName].size > 500000) {
                    this.imageErrors[refName] = "Please select an image less than 500 KB";   
                    this.imageSuccess[refName] = "";
                } else {
                    this.imageErrors[refName] = "";
                    this.imageSuccess[refName] = "File OK";
                }
            },

            
            initializeFormData() {
                this.formData.reference = '';
                this.formData.lender_type = 1;
                this.formData.licence_type = 1;
                this.formData.licenceImage = '';
                this.formData.regCertificate = '';
                this.formData.nin_number = '';
                this.formData.managed_account = false;
            }
        },
    };
</script>