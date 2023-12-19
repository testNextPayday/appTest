<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
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
            <div class="col-sm-6 col-md-4" v-for="employment in employments">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-briefcase text-success" v-if="employment.is_current"></i>
                        <i class="fa fa-briefcase" v-else></i>
                        {{employment.position}} - {{employment.department}} Department
                        <span class="pull-right">
                            <a href="#" @click="editEmployment(employment.id)">
                                <i class="fa fa-pencil text-primary"></i>
                            </a>
                            &nbsp;
                            <a href="#" @click="deleteEmployment(employment.id)">
                                <i class="fa fa-trash text-danger"></i>
                            </a>
                        </span>
                        <span class="clearfix"></span>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
               <!--              <li class="list-group-item">
                                Date Employed: <b>{{employment.date_employed}}</b>
                            </li>
                            <li class="list-group-item">
                                Net Pay: <b>{{employment.net_pay}}</b>
                            </li>
                            <li class="list-group-item">
                                Supervisor: <b>{{employment.supervisor_name}}</b>
                            </li>
                            <li class="list-group-item">
                                MDA: <b>{{employment.mda}}</b>
                            </li> -->
                            <li class="list-group-item dropdown-header text-center">
                                <b>EMPLOYER</b>
                            </li>
                            <li class="list-group-item">
                                Name: <b>{{employment.employer.name}}</b>
                            </li>

                             <li class="list-group-item">
                                Payroll ID: <b>{{employment.payroll_id}}</b>
                            </li>

                            <li class="list-group-item">
                                Status: 
                                <b>
                                    <span class="text-success" v-if="employment.employer.is_verified === 3">Verified</span>
                                    <span class="text-info" v-else-if="employment.employer.is_verified === 1">Undergoing Verification</span>
                                    <span class="text-danger" v-else-if="employment.employer.is_verified === 2">Verification Denied</span>
                                    <span class="text-warning" v-else>Not Verified &nbsp;&nbsp;&nbsp;
                                        <a class="badge badge-pill ball" href="javascript:;" @click="startEmployerVerification(employment.employer.id, employment.id)" title="Click me to learn more" v-if="!employment.employer.is_verified">
                                            <i class="icon-info"></i>
                                        </a>
                                    </span>
                                    
                                </b>
                            </li>
                            <li class="list-group-item dropdown-header text-center">
                                <b>DOCUMENTS</b>
                            </li>
                            <li class="list-group-item">
                                Employment Letter &nbsp;&nbsp;<a target="_blank" :href="employment.employment_letter" class="badge badge-success">view</a>
                            </li>
                            <li class="list-group-item">
                                Confirmation Letter &nbsp;&nbsp;<a target="_blank" :href="employment.confirmation_letter" class="badge badge-primary">view</a>
                            </li>
                            <li class="list-group-item">
                                Work ID Card &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a target="_blank" :href="employment.passport" class="badge badge-warning">view</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--/.col-->
            
            <modal v-if="editing" @close="editing = false">
                <div slot="header">
                    <h4>Update Employment
                        <span class="justify-content-right"  v-if="!loading">
                            <button type="button" class="close" @click="editing = false">&times;</button>
                        </span>
                    </h4>
                </div>
    
                <div slot="body">
                    <div>
                        <h5 class="text-danger text-center">
                             {{error_message}}   
                        </h5>
                        <ul v-if="errorBag.length > 0">
                            <li v-for="error in errorBag" class="text-danger">{{error}}</li>
                        </ul>
                        
                        <div class="form-group">
                            <label class="control-label" for="position"><strong>Position</strong></label>
                            <div class="input-group mb-3">
                                <span class="input-group-addon"><i class="icon-layers"></i></span>
                                <input type="text" name="position" id="position"
                                    class="form-control" required v-model="update.position"
                                    placeholder="Your position at work" title="Your position at work">
                            </div>    
                        </div>
                            
                        <div class="form-group">
                            <label class="control-label" for="department"><strong>Department</strong></label>
                            <div class="input-group mb-3">
                                <span class="input-group-addon"><i class="icon-briefcase"></i></span>
                                <input type="text" name="department" id="department"
                                    class="form-control" required v-model="update.department"
                                    placeholder="Your department at work" title="Your department at work">
                            </div>    
                        </div>
                        
                        <!-- <div class="form-group">
                            <label class="control-label" for="gross_pay"><strong>Gross Pay</strong></label>
                            <div class="input-group mb-3">
                                <span class="input-group-addon">â‚¦</span>
                                <input type="number" name="gross_pay" id="gross_pay"
                                    class="form-control" required v-model="update.gross_pay"
                                    placeholder="Gross Pay" title="Gross Pay">
                            </div>    
                        </div> -->
                            
                        <div class="form-group">
                            <label class="control-label" for="net_pay"><strong>Net Salary</strong></label>
                            <div class="input-group mb-3">
                                <span class="input-group-addon">â‚¦</span>
                                <input type="number" name="net_pay" id="net_pay"
                                    class="form-control" required v-model="update.net_pay"
                                    placeholder="Net Salary" title="Net Salary">
                            </div>    
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="payroll_id"><strong>Payroll ID</strong></label>
                            <div class="input-group mb-3">
                                <!-- <span class="input-group-addon">â‚¦</span> -->
                                <input type="text" name="payroll_id" id="payroll_id"
                                    class="form-control" required v-model="update.payroll_id"
                                    placeholder="Payroll ID" title="Payroll ID">
                            </div>    
                        </div>
                        
                    </div>
                </div>
    
                <div slot="footer">
                    <div>
                        <button class="btn btn-info" :disabled="loading" @click="updateEmployment">
                            <i :class="buttonClass"></i>
                            Update Employment
                        </button>
                    </div>
                </div>
            </modal>
        </div>
        
        <div class="row justify-content-center" v-else>
          
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <strong>New</strong>
                        <small>Employment</small>
                    </div>
                        
                    <div class="card-body">
                        <form method="POST" action="#" @submit.prevent="saveEmployment">
                            <h5 class="text-danger text-center">
                                 {{error_message}}   
                            </h5>
                            <ul v-if="errorBag.length > 0">
                                <li v-for="error in errorBag" class="text-danger">{{error}}</li>
                            </ul>
                            
                            <div v-if="page === 1">
                                
                                <div class="form-group">
                                    <label class="control-label" for="employer_state"><strong>Select your employer state</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-map"></i></span>
                                        <select class="form-control" name="employer_state" id="employer_state" v-model="employment.employer_state">
                                            <option disabled>Choose your employer state</option>
                                            <template v-for="state in statesCopy">
                                                <option :value="state">{{state}}</option>    
                                            </template>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="employer_id"><strong>Select An Employer</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-user"></i></span>
                                        <select class="form-control" name="employer_id" id="employer_id" v-model="employment.employer_id" required>
                                            <option disabled>Choose your employer</option>
                                            <template v-for="employer in stateEmployers">
                                                <option :value="employer.id">{{employer.name}} - {{employer.is_verified == 3 ? 'Verified': 'Not Verified'}}</option>    
                                            </template>
                                        </select>
                                    </div>
                                </div>
                                <p class="text-primary">Can't find your employer? <a class="badge badge-danger" style="color:white; cursor:pointer" @click="addEmpFunC()">Add New</a></p>
                                

                                <div v-if="employment.employer_id !== '' && !addEmp">
                                    <div class="form-group">
                                        <label class="control-label" for="net_pay"><strong>Net Salary</strong></label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-addon">â‚¦</span>
                                            <input type="text" name="net_pay" id="net_pay"
                                                class="form-control" required v-model="employment.net_pay"
                                                placeholder="Net Salary" title="Net Salary">
                                        </div>    
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="payroll_id"><strong>MDA</strong></label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-addon"><i class="icon-layers"></i></span>
                                            <input type="text" name="mda" id="mda"
                                                class="form-control" required v-model="employment.mda"
                                                placeholder="MDA" title="MDA">
                                        </div>    
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="department"><strong>Department</strong></label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-addon"><i class="icon-briefcase"></i></span>
                                            <input type="text" name="department" id="department"
                                                class="form-control" required v-model="employment.department"
                                                placeholder="Your department at work" title="Your department at work">
                                        </div>    
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="payroll_id"><strong>Payroll ID</strong></label>
                                        <div class="input-group mb-3">
                                            <!-- <span class="input-group-addon">â‚¦</span> -->
                                            <input type="text" name="payroll_id" id="payroll_id"
                                                class="form-control" v-model="employment.payroll_id"
                                                placeholder="Payroll ID" title="Payroll ID">
                                        </div>    
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success pull-right" v-if="page === 1" :disabled="loading">
                                        <i :class="buttonClass"></i>
                                        Save
                                    </button>
                                </div>                         
                            </div>
                        <!-- <div v-if="employment.employer_id !== 268">
                             <div v-if="page === 2">
                                    
                                 <div class="form-group">
                                    <label class="control-label" for="gross_pay"><strong>Gross Pay</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon">â‚¦</span>
                                        <input type="text" name="gross_pay" id="gross_pay"
                                            class="form-control" required v-model="employment.gross_pay"
                                            placeholder="Gross Pay" title="Gross Pay">
                                    </div>    
                                </div>
                                    
                                 <div class="form-group">
                                    <label class="control-label" for="net_pay"><strong>Net Salary</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon">â‚¦</span>
                                        <input type="text" name="net_pay" id="net_pay"
                                            class="form-control" required v-model="employment.net_pay"
                                            placeholder="Net Salary" title="Net Salary">
                                    </div>    
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="payroll_id"><strong>Payroll</strong></label>
                                    <div class="input-group mb-3">
                                         <span class="input-group-addon">â‚¦</span> 
                                         <input type="text" name="payroll_id" id="payroll_id"
                                            class="form-control" required v-model="employment.payroll_id"
                                            placeholder="Payroll ID" title="Payroll ID">
                                    </div> 
                                </div>
                            </div>-->
                            
                             <div v-if="page === 3">
                                <p class="text-center"><b>DOCUMENT UPLOADS (OPTIONAL) </b></p>
                                <p class="text-center"><strong><small>Max Upload Size: 1MB</small></strong></p>
                                
                                <div class="row">
                                    
                                    <div class="col-sm-12 text-center">
                                        <p class="text-muted">* {{fileLabels[fileNumber - 1]}}</p>
                                        <input type="file" id="file" ref="file" @change="handleFileUpload()"/>
                                        <label for="file"><strong>{{uploading ? 'Uploading...' : 'Choose a file'}}</strong></label>
                                    </div>
                                    
                                    <div class="col-sm-12" v-if="uploading">
                                        <b-progress :value="uploadPercentage" :max="100" show-progress animated></b-progress>
                                    </div>
                                    <hr/>
                                    
                                    <div class="col-sm-12 text-center">
                                        <button type="button" class="mt-4" @click.prevent="uploadDocument">Upload</button>
                                    </div>
                                    
                                    <hr/>
                                    <div class=" col-sm-12 lead text-center"><small><strong>FILE {{fileNumber}} / 3</strong></small></div>
                                    
                                </div>
                            </div>
                            
                            <!-- <div v-if="page < 3">
                                <button type="button" class="btn btn-primary" v-if="page === 2" @click="--page" :disabled="loading">
                                    <i class="fa fa-arrow-left"></i>
                                    Prev
                                </button>
                                <button type="button" class="btn btn-primary pull-right" v-if="page < 2" @click="page++" :disabled="loading">
                                    <i class="fa fa-arrow-right"></i>
                                    Next
                                </button>
                                <button type="submit" class="btn btn-success pull-right" v-if="page === 2" :disabled="loading">
                                    <i :class="buttonClass"></i>
                                    Save
                                </button>
                            </div> -->
                            <div v-else></div>                          
                        </form>
                    </div>
                </div>
            </div>
            <employer :current-state="employment.employer_state" 
                    :states="states"
                    :url="employerAddUrl"
                    :show="showEmployerModal" 
                    @employer-added="addNewEmployer" 
                    @modal-closed="showEmployerModal = false"></employer>
        </div>
        
        <employer-verification :show="showModal"
                    :verification-fee="verificationFee"
                    :wallet="wallet" 
                    :email="email"
                    :pay-key="payKey"
                    :employer-id="employerId"
                    @modal-closed="showModal = false"
                    @employer-verification-request-placed="verificationRequestPlaced"></employer-verification>
        
    </div>
</template>
<script>
    import Employer from './../Employer';
    import EmployerVerification from './../EmployerVerification';
    export default {
        props: ['userEmployments', 'allEmployers', 'employerStates', 'verificationFee', 'wallet', 'email', 'payKey', 'employerAddUrl', 'states'],
        data() {
            return {
                employment: {
                    //position: '',
                    department: '',
                    date_employed: '',
                    date_confirmed: '',
                    payroll_id: '',
                    //monthly_salary: '',
                    //gross_pay: '',
                    net_pay: '',
                    employer_state: '',
                    employer_id: '',
                    // supervisor_name: '',
                    // supervisor_phone: '',
                    // supervisor_grade: '',
                    // supervisor_email: '',
                    mda: ''
                },
                
                uploading:false,
                file: '',
                uploadPercentage: 0,
                fileLabels: [
                    'Letter of employment',
                    'Letter of Confirmation',
                ],
                dbFileLabels: [
                    'employment_letter',
                    'confirmation_letter',
                ],
                fileNumber: 1,
                newEmploymentId: 0,
                
                employments: [],
                employers: [],
                statesCopy: [],
                stateEmployers: [],
                mode: false,
                page: 1,
                loading: false,
                error_message: '',
                errorBag: [],
                buttonClass: {
                    fa: true,
                    "fa-check-circle-o": true,
                    "fa-spin": false,
                    "fa-spinner": false
                },
                showModal: false,
                showEmployerModal: false,
                addEmp:false,
                employerId: 0,
                employmentId: 0,
                reader: {},
                editing: false,
                update: {
                    //position: '',
                    department: '',
                    //gross_pay: '',
                    net_pay: '',
                    employment_id: 0,
                    payroll_id : ''
                }
            };
        },
        
        mounted() {
            this.initializeEmploymentData();  
        },
        
        methods: {

            addEmpFunC(){
                this.showEmployerModal = true;
                this.addEmp = true;
            },
            initializeEmploymentData() {
                this.employerStates.forEach(states => {
                    
                    states.state.split(',').forEach(state => {
                        
                        if (!this.statesCopy.includes(state)) {
                            this.statesCopy.push(state);
                        }
                        
                    });
                    
                });
                
                this.statesCopy = this.statesCopy.sort();
                
                this.userEmployments.forEach(employment => {
                    this.employments.push(employment);  
                });
                this.allEmployers.forEach(employer => {
                    this.employers.push(employer); 
                });
                this.setStateEmployers();
            },
            
            setStateEmployers() {
                this.stateEmployers = [];
                
                this.employers.forEach(employer => {
        
                    if(this.employment.employer_state && 
                        employer.state && employer.state.indexOf(this.employment.employer_state) !== -1) {
                        this.stateEmployers.push(employer);
                    } 
                });
            },
            
            saveEmployment(event) {
                let valid = this.validateData();
                if(valid) {
                    this.clearError();
                    this.startLoading();
                    axios.post(`/profile/employment/add`, this.employment).then((response) => {
                        if(response.data.status === 1) {
                            if(this.employment.employer_id === 268){
                                this.employments.push(response.data.employment);                                
                                alert('Employment Registration Complete');
                                this.mode = !this.mode;
                                this.page = 1;
                            }else{
                                this.employments.push(response.data.employment);
                                //this.mode = !this.mode;
                                this.page = 3;
                                this.fileNumber = 1;
                                this.newEmploymentId = response.data.employment.id;
                                alert('Complete employment registration by uploading your work documents');
                            }
                            
                        } else {
                            if (response.data.message) this.setError(response.data.message);
                            else this.setError('An error occurred. Please try again');
                        }
                        this.stopLoading();
                    }).catch((error) => {
                        if(error.response && error.response.status === 422) {
                            this.setError(error.response.data.message);
                            let errorMessages = Object.keys(error.response.data.errors);
                            errorMessages.forEach((errorKey) => {
                                this.errorBag.push(error.response.data.errors[errorKey][0])
                            });
                        } else {
                            this.setError(error.message);
                        }
                        this.stopLoading();
                    });
                }
            },
            
            editEmployment(id) {
                this.editing = true;
                let employment = this.employments.find(employment => employment.id === id);
                Object.keys(this.update).forEach(key => {
                    this.update[key] = employment[key];
                });
                this.update.employment_id = id;
                this.editing = true;
            },
            
            updateEmployment() {
                Object.keys(this.update).forEach(key => {
                    if (!this.update[key]) return alert(`Please provide ${key.replace("_", " ")}`);
                });
                
                this.clearError();
                this.startLoading();
                axios.post(`/profile/employment/update`, this.update).then((response) => {
                    if(response.data.status === 1) {
                        let employment = this.employments.find(employment => employment.id === this.update.employment_id);
                        Object.keys(this.update).forEach(key => {
                             employment[key] = this.update[key];
                        });
                        this.editing = false;
                    } else {
                        if (response.data.message) this.setError(response.data.message);
                        else this.setError('An error occurred. Please try again');
                    }
                    this.stopLoading();
                }).catch((error) => {
                    if(error.response && error.response.status === 422) {
                        this.setError(error.response.data.message);
                        let errorMessages = Object.keys(error.response.data.errors);
                        errorMessages.forEach((errorKey) => {
                            this.errorBag.push(error.response.data.errors[errorKey][0])
                        });
                    } else {
                        this.setError(error.message);
                    }
                    this.stopLoading();
                });
            },
            
            deleteEmployment(id) {
                let proceed = confirm('Are your sure?');
                if(!proceed) {
                    return;
                }
                this.startLoading();
                axios.get(`profile/employment/delete/${id}`).then((response) => {
                    if(response.data.status === 1) {
                        let employment = this.employments.find((employment) => employment.id === id);
                        let index = this.employments.indexOf(employment);
                        if(index !== -1) {
                            this.employments.splice(index, 1);
                        }
                    } else {
                        alert(response.data.message)
                        this.setError(response.data.message);
                    }
                    this.stopLoading();
                }).catch((error) => {
                    this.setError(error.message);
                    this.stopLoading();
                });
            },
            
            addNewEmployer(event) {
                this.employers.push(event.employer);
                
                if (!event.employer.state) return;
                
                event.employer.state.split(',').forEach(state => {
                    if (!this.statesCopy.includes(state)) {
                        this.statesCopy.push(state);
                    }
                    this.employment.employer_state = state;
                });
                
                this.employment.employer_id = event.employer.id;
                this.showEmployerModal = false;
                
            },
            
            startEmployerVerification(employerId, employmentId) {
                this.employerId = employerId;
                this.employmentId = employmentId;
                this.showModal = true;
            },
            
            verificationRequestPlaced(event) {
                let employment = this.employments.find(employment => employment.id === this.employmentId);
                if(employment) {
                    let employer = employment.employer;
                    employer.is_verified = event.employer.is_verified;
                }
                this.showModal = false;
            },
            
            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length) return;
                let elementId = e.target.id;
                this.createImage(files[0], elementId);
            },

            createImage(file, elementId) {
                this.reader[elementId] = new FileReader();
                let vm = this;
                this.reader[elementId].onload = (e) => {
                    vm.employment[elementId] = e.target.result;
                };
                this.reader[elementId].readAsDataURL(file);
            },
            
            handleFileUpload(){
                this.file = this.$refs.file.files[0];
                //console.log(this.$refs.file.files);
            },
            
            async uploadDocument(){
                if (this.file === '') return alert("Please select a file");
                let formData = new FormData();
                formData.append('file', this.file);
                formData.append('employment_id', this.newEmploymentId);
                formData.append('label', this.dbFileLabels[this.fileNumber - 1]);    
                
                this.uploading = true;
                this.startLoading();
                await axios.post( '/profile/employment/upload-documents', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                  
                    onUploadProgress: function( progressEvent ) {
                        this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
                    }.bind(this)
                
                    
                }).then((response) => {
                    if(response.data.status === 1) {
                        this.file = '';
                         this.$refs.file.value = "";
                        //update employment
                        let employment = this.employments.find((employment) => employment.id === response.data.employment.id);
                        if (employment) {
                            employment[this.dbFileLabels[this.fileNumber - 1]] = response.data.employment[this.dbFileLabels[this.fileNumber - 1]];
                        }
                        
                        this.fileNumber++;
                        if (this.fileNumber > 2) {
                            alert('Success!');
                            this.mode = !this.mode;
                            this.page = 1;
                        }
                    } else {
                        if (response.data.message) this.setError(response.data.message);
                        else this.setError('An error occurred. Please try again');
                    }
                    this.stopLoading();
                    this.uploading = false;
                    this.uploadPercentage = 0;
                }).catch((error) => {
                    if(error.response && error.response.status === 422) {
                        this.setError(error.response.data.message);
                        let errorMessages = Object.keys(error.response.data.errors);
                        errorMessages.forEach((errorKey) => {
                            this.errorBag.push(error.response.data.errors[errorKey][0])
                        });
                    } else {
                        this.setError(error.message);
                    }
                    this.stopLoading();
                    this.uploading = false;
                    this.uploadPercentage = 0;
                });
            },
            
            validateData() {
                // if(this.employment.position == '') {
                //     this.setError("Please enter your position");
                //     this.switchPage(1);
                //     return false;
                // }
                
                if(this.employment.department == '') {
                    this.setError("Please enter your department");
                    this.switchPage(1);
                    return false;
                }
                
                // if(this.employment.date_employed == '') {
                //     this.setError("Please enter the date you were employed");
                //     this.switchPage(1);
                //     return false;
                // }
                
                // if(this.employment.date_confirmed == '') {
                //     this.setError("Please enter the date this employment was confirmed");
                //     this.switchPage(1);
                //     return false;
                // }
                
                // if(this.employment.monthly_salary == '') {
                //     this.setError("Please enter your specified monthly salary");
                //     this.switchPage(2);
                //     return false;
                // }    
                
                // if(this.employment.gross_pay == '') {
                //     this.setError("Please enter your gross pay");
                //     this.switchPage(2);
                //     return false;
                // }
                
                if(this.employment.net_pay == '') {
                    this.setError("Please enter your net salary");
                    this.switchPage(2);
                    return false;
                }
                    
                if(!this.employment.employer_id) {
                    this.setError("Please select an employer state and an employer. If they don't exist, you can add one");
                    this.switchPage(1);
                    return false;
                }   
                
                // if(this.employment.supervisor_name == '') {
                //     this.setError("Please enter the name of your supervisor");
                //     this.switchPage(2);
                //     return false;
                // }
                
                // if(this.employment.supervisor_phone == '') {
                //     this.setError("Please enter the phone number of your supervisor");
                //     this.switchPage(2);
                //     return false;
                // }
                
                // if(this.employment.supervisor_grade == '') {
                //     this.setError("Please enter the grade of your supervisor");
                //     this.switchPage(2);
                //     return false;
                // }
                
                // if(this.employment.supervisor_email !== '' && !this.validateEmail(this.employment.supervisor_email)) {
                //     this.setError("Please enter a valid email for your supervisor");
                //     this.switchPage(2);
                //     return false;
                // }
                
                // if(this.employment.employment_letter == '') {
                //     this.setError("Please upload your employment letter");
                //     this.switchPage(4);
                //     return false;
                // }
                
                // if(this.employment.confirmation_letter == '') {
                //     this.setError("Please upload your confirmation letter");
                //     this.switchPage(4);
                //     return false;
                // }
                
                // if(this.employment.work_id_card == '') {
                //     this.setError("Please upload your work_id_card");
                //     this.switchPage(4);
                //     return false;
                // }
                return true;
            },
            
            switchMode() {
                this.mode = !this.mode;
            },
            
            switchPage(number) {
                this.page = number;  
            },
            
            validateEmail(email) {
                return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email) ? true: false;
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
        },
        
        components: {
            'employer': Employer,
            'employer-verification': EmployerVerification, 
        },
        
        watch: {
            "employment.employer_state": function(current) {
                this.setStateEmployers();
            }
        }
    };
</script>
<style scoped>
.ball {
    background-color:#dc0301;
    color:white;
    font-size:bold;
    cursor: pointer;
    animation: bounce 0.7s infinite alternate;
    -webkit-animation: bounce 0.7s infinite alternate;
}
@keyframes bounce {
  from {
    transform: translateY(0px);
  }
  to {
    transform: translateY(-8px);
  }
}
@-webkit-keyframes bounce {
  from {
    transform: translateY(0px);
  }
  to {
    transform: translateY(-8px);
  }
}

#file {
	width: 0.1px;
	height: 0.1px;
	opacity: 0;
	overflow: hidden;
	position: absolute;
	z-index: -1;
}

#file + label {
    font-size: 1.25em;
    font-weight: 700;
    color: white;
    background-color: #d3394c;
    text-overflow: elipsis;
    display: inline-block;
    cursor: pointer;
    padding: .5em 2em;
}

#file:focus + label,
#file + label:hover {
    background-color: #4b0f31;
}

#file:focus + label {
	outline: 1px dotted #000;
	outline: -webkit-focus-ring-color auto 5px;
}

#file + label * {
    pointer-events: none;
}
</style>