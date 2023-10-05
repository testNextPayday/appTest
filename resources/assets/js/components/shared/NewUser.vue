<template>
    <div class="card">

        <form method="POST" @submit.prevent="submitNewUserForm" enctype="multipart/form-data">
            
            <div class="card-body">
                <div v-if="section === 1">
                    <h4 class="lead">Basic Information</h4>
                    <hr/>
                    
                    <div class="row">
                        <div class="form-group col-sm-4 col-xs-12">
                            <label><b>FULL NAME </b></label>
                            <input type="text" class="form-control" v-model="formData.surname"  name="surname" placeholder="Full name" required>
                        </div>
                         <div class="form-group col-sm-4 col-xs-12">
                            <label>Email </label>
                            <input type="email" class="form-control" v-model="formData.email"  name="email" placeholder="Email Address" required>
                        </div>
                        
                        <div class="form-group col-sm-4 col-xs-12">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" v-model="formData.phone"  name="phone" placeholder="Phone Number" required>
                        </div>
                       
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-sm-4 col-xs-12">
                            <label>Gender</label>
                            <select name="gender" class="form-control" v-model="formData.gender">
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-4 col-xs-12">
                            <label>Date of Birth </label>
                            <input type="date" class="form-control" v-model="formData.dob" name="dob" placeholder="Date of Birth" required>
                        </div>
                        
                        <div class="form-group col-sm-4 col-xs-12">
                            <label>Occupation/Vocation </label>
                            <input type="text" class="form-control" v-model="formData.occupation"  name="occupation" placeholder="Occupation" required>
                        </div>
                    </div>
                    
                    <div class="row">                        
                        <div class="form-group col-sm-4 col-xs-12">
                            <label>Address </label>
                            <input type="text" class="form-control" v-model="formData.address"  name="address" placeholder="Address" required>
                        </div>
                        <!-- <div class="form-group col-sm-4 col-xs-12">
                            <label>Marital Status</label>
                            <select name="marital_status" class="form-control" v-model="formData.marital_status">
                                <option value="1">Single</option>
                                <option value="2">Married</option>
                                <option value="3">Divorced</option>
                                <option value="4">Widowed</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-4 col-xs-12">
                            <label>Number of Children </label>
                            <input type="number" class="form-control" v-model="formData.no_of_children"  name="no_of_children" placeholder="Number of Children" required>
                        </div> -->
                    </div>
                    <!--<div class="row">                        
                         <div class="form-group col-sm-4 col-xs-12">
                            <label>City/Town</label>
                            <input type="text" class="form-control" v-model="formData.city"  name="city" placeholder="City" required>
                        </div> 
                    </div>-->
                    
                    <!-- <div class="row">
                        <div class="form-group col-sm-4 col-xs-12">
                            <label>LGA </label>
                            <input type="text" class="form-control" v-model="formData.lga"  name="lga" placeholder="LGA" required>
                        </div> -->
                        
                        <!-- <div class="form-group col-sm-4 col-xs-12">
                            <label>State</label>
                            <select id="state" class="form-control" required v-model="formData.state"
                                name="state" title="State">
                                <option v-for="state in states" :value="state">{{state}}</option>
                            </select>
                        </div>
                    </div> -->
                    
                    <hr/>
                    <h4 class="lead">Bank Information</h4>
                    <hr/>
                    <div class="row">
                        <div class="form-group col-sm-4 col-xs-12">
                            <label>Bank Name </label>
                            <select name="bank_name" id="bank_name"
                                class="form-control" required v-model="formData.bankCode">
                                <option v-for="bankCode in bankCodes" :value="bankCode">{{banks[bankCode]}}</option>                                        
                            </select>
                        </div>
                        
                        <div class="form-group col-sm-4 col-xs-12">
                            <label>Account Number</label>
                            <input type="text" class="form-control" v-model="formData.accountNumber"  name="accountNumber" placeholder="Enter Account Number" required>
                        </div>
                        
                        <div class="form-group col-sm-4 col-xs-12">
                            <label>BVN</label>
                            <input type="text" class="form-control" v-model="formData.bvn"  name="bvn" placeholder="Enter BVN" required>
                        </div>
                    </div>
                    
                    <!-- <hr/>
                    <h4 class="lead">Next of Kin Information</h4>
                    <hr/> -->
                    <!-- <div class="row">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label>Name </label>
                            <input type="text" class="form-control" v-model="formData.next_of_kin"  name="next_of_kin" placeholder="Next of Kin's Name" required>
                        </div>
                        
                        <div class="form-group col-sm-6 col-xs-12">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" v-model="formData.next_of_kin_phone"  name="next_of_kin_phone" placeholder="Next of Kin's Phone Number" required>
                        </div>
                    </div> -->
                    <!-- <div class="row">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label>Address </label>
                            <input type="text" class="form-control" v-model="formData.next_of_kin_address"  name="next_of_kin_address" placeholder="Address of Next of Kin" required>
                        </div>
                        
                        <div class="form-group col-sm-6 col-xs-12">
                            <label>Relationship with Next of Kin</label>
                            <input type="text" class="form-control" v-model="formData.relationship_with_next_of_kin"  name="relationship_with_next_of_kin" placeholder="Relationship with next of Kin" required>
                        </div>
                    </div> 
                    <hr/>-->
                    
                </div>
                
                <div v-if="section === 2">
                    <h4 class="lead">Work Information</h4>
                    <hr/>
                    
                    <div class="row">
                        <div class="form-group col-sm-4 col-xs-12">
                            <label class="control-label" for="employer_id"><strong>Select Employer state</strong></label>
                            <select class="form-control" name="employer_id" id="employer_id" v-model="formData.employer_state">
                                <option disabled>Choose user's employer state</option>
                                <template v-for="state in statesCopy">
                                    <option :value="state">{{state}}</option>    
                                </template>
                            </select>
                        </div>
                                
                        <div class="form-group col-sm-4 col-xs-12">
                            <label class="control-label" for="employer_id"><strong>Select Employer</strong></label>
                            <select class="form-control" name="employer_id" id="employer_id" @change="onChangeEmployer($event)" v-model="formData.employer_id" required>
                                <option disabled>Choose user's employer</option>
                                <template v-for="employer in stateEmployers">
                                    <option :value="employer.id">{{employer.name}}</option>    
                                </template>
                            </select>
                        </div>

                        <div class="form-group col-sm-4 col-xs-12">
                            <label class="control-label" for="mda"><strong>MDA</strong></label>
                            <input type="text" name="mda" id="mda"
                                class="form-control" required v-model="formData.mda"
                                placeholder="MDA" title="mda">
                        </div>
                                
                        <!-- <p class="text-primary col-sm-12">Can't find employer? <a class="badge badge-danger" style="color:white; cursor:pointer" @click="showEmployerModal = true">Add New</a></p> -->
                    </div>
                    <br/>
                    <div class="row">
                        <!-- <div class="form-group col-sm-4 col-xs-12">
                            <label class="control-label" for="position"><strong>Position/Grade</strong></label>
                            <input type="text" name="position" id="position"
                                class="form-control" required v-model="formData.position"
                                placeholder="Position at work" title="Position at work">
                        </div> -->

                        <div class="form-group col-sm-4 col-xs-12" v-if="settings.payrollID == true">
                            <label class="control-label" for="payroll_id"><strong>Pay Roll number</strong></label>
                            <input type="text" name="payroll_id" id="payroll_id"
                                class="form-control" required v-model="formData.payroll_id"
                                placeholder="payroll number at work" title="payroll number at work">
                        </div>

                        
                        <div class="form-group col-sm-4 col-xs-12" v-if="settings.validID == true">
                            <label class="control-label" for="validId"><strong>Valid ID</strong></label>
                            <input type="file" name="validId" v-on:change="onUploadvalidId" required class="form-control">
                        </div>

                        <div class="form-group col-sm-4 col-xs-12" v-if="settings.workID == true">
                            <label class="control-label" for="workId"><strong>Work ID</strong></label>
                            <input type="file" v-on:change="onUploadworkId" required class="form-control">
                        </div>

                       <div class="form-group col-sm-4 col-xs-12" v-if="settings.passport == true">
                            <label class="control-label" for="passport"><strong>Passport</strong></label>
                            <input type="file" name="passport" v-on:change="onUploadpassport" placeholder="Enter Work ID" id="passport" required class="form-control">
                        </div>

                        <div class="form-group col-sm-4 col-xs-12" v-if="settings.confirmLetter == true, employerId != 353">
                            <label class="control-label" for="passport"><strong>Confirmation letter</strong></label>
                            <input type="file" name="confirmation" v-on:change="onUploadconfirmation" id="confirmation" required class="form-control">
                        </div>


                        <div class="form-group col-sm-4 col-xs-12" v-if="settings.appLetter == true, employerId != 353">
                            <label class="control-label" for="appointment"><strong>Appointment letter</strong></label>
                            <input type="file" name="appointment" v-on:change="onUploadappointment" id="appointment" required class="form-control">
                        </div>

                        <div class="form-group col-sm-4 col-xs-12" v-if="employerId != 353">
                            <label class="control-label" for="appForm"><strong>Application form</strong></label>
                            <input type="file" name="appForm" v-on:change="onUploadappForm" id="appForm" class="form-control">
                        </div>
                                        
              <!--           <div class="form-group col-sm-4 col-xs-12">
                            <label class="control-label" for="department"><strong>Department</strong></label>
                            <input type="text" name="department" id="department"
                                class="form-control" required v-model="formData.department"
                                placeholder="Department at work" title="Your department at work">
                        </div> -->
                        
                        <div class="form-group col-sm-4 col-xs-12">
                            <label class="control-label" for="date_employed"><strong>Date of Employment</strong></label>
                            <input type="date" name="date_employed" id="date_employed"
                                class="form-control" required v-model="formData.date_employed"
                                placeholder="Date of employment (mm/dd/YYYY)" title="Date of employment">
                        </div>

                        <div class="form-group col-sm-4 col-xs-12">
                            <label class="control-label" for="date_confirmed"><strong>Date of Employment Confirmation</strong></label>
                            <input type="date" name="date_confirmed" id="date_confirmed"
                                class="form-control" required v-model="formData.date_confirmed"
                                placeholder="Date of employment confirmation" title="Date of employment confirmation">
                        </div>

                        
                    </div>
                    <div class="row">
                        
                            
                        <!-- <div class="form-group col-sm-4 col-xs-12">
                            <label class="control-label" for="gross_pay"><strong>Gross Pay</strong></label>
                            <input type="text" name="gross_pay" id="gross_pay"
                                class="form-control" required v-model="formData.gross_pay"
                                placeholder="Gross Pay" title="Gross Pay">
                        </div>-->
                        
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="net_pay"><strong>Net Salary</strong></label>
                            <input type="text" name="net_pay" id="net_pay"
                                class="form-control" required v-model="formData.net_pay"
                                placeholder="Net Salary" title="Net Salary">
                        </div>
                    </div>
<!--         
                    <div class="row">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label" for="supervisor_name"><strong>Supervisor Name</strong></label>
                            <input type="text" name="supervisor_name" id="supervisor_name"
                                class="form-control" required v-model="formData.supervisor_name"
                                placeholder="Name of Supervisor" title="Name of Supervisor">
                        </div>
                        
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label" for="supervisor_email"><strong>Supervisor Email</strong></label>
                            <input type="email" name="supervisor_email" id="supervisor_email"
                                class="form-control" required v-model="formData.supervisor_email"
                                placeholder="Email of Supervisor" title="Email of Supervisor">
                        </div>                        
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label" for="supervisor_phone"><strong>Supervisor Phone</strong></label>
                            <input type="number" name="supervisor_phone" id="supervisor_phone"
                                class="form-control" required v-model="formData.supervisor_phone"
                                placeholder="Supervisor Phone" title="Supervisor Phone">
                        </div>
                            
                        <div class="form-group col-sm-6 col-xs-12">
                            <label class="control-label" for="employer_address"><strong>Supervisor Grade</strong></label>
                            <input type="text" name="supervisor_grade" id="supervisor_grade"
                                class="form-control" required v-model="formData.supervisor_grade"
                                placeholder="Grade of Supervisor" title="Grade of Supervisor">
                        </div>                        
                    </div> -->
                </div>

               
                
                <!-- <div v-if="section === 3">
                    <p class="text-center"><b>DOCUMENT UPLOADS</b></p>
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
                            <b-btn class="mt-4" @click="uploadDocument">Upload</b-btn>
                        </div>
                        
                        <hr/>
                        <div class=" col-sm-12 lead text-center"><small><strong>FILE {{fileNumber}} / 3</strong></small></div>
                        
                    </div>
                </div> -->
                
                <div v-if="section < 3">
                    <button type="button" class="btn btn-sm btn-primary" :disabled="loading" @click="++section;" v-if="section !== 2">
                        <i class="fa fa-arrow-right"></i>
                        Next
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" :disabled="loading" @click="--section" v-if="section !== 1">
                        <i class="fa fa-arrow-left"></i>
                        Prev
                    </button>
                    <button type="submit" class="btn btn-sm btn-warning pull-right" :disabled="loading" v-if="section === 2">
                        <i :class="spinClass"></i> Submit
                    </button>
                </div>
            </div>
        </form>
        
        <employer :current-state="formData.employer_state"
                :states="states"
                :show="showEmployerModal" 
                :url="employerAddUrl"
                @employer-added="addNewEmployer" 
                @modal-closed="showEmployerModal = false"></employer>
    </div>
</template>
<script>
    import Employer from './../Employer';
    import { utilitiesMixin } from './../../mixins';
    
    export default {
        props: ['allEmployers', 'employerStates', 'url', 'employerAddUrl', 'uploadsUrl', 'banks', 'states'],
        
        mixins: [utilitiesMixin],
        
        data() {
            return {
                formData: {
                    user_type: 1,
                    firstname: '',
                    midname: '',
                    surname: '',
                    email: '',
                    phone: '',
                    dob: '',
                    gender: 1,
                    marital_status: 1,
                    no_of_children: 0,
                    occupation: '',
                    address: '',
                    city: '',
                    lga: '',
                    state: '',
                    next_of_kin: '',
                    next_of_kin_phone: '',
                    next_of_kin_address: '',
                    relationship_with_next_of_kin: '',
                    employer_state: '',
                    employer_id: 0,
                    position: '',
                    department: '',
                    payroll_id:'',
                    date_employed: '',
                    mda:'',
                    date_confirmed: '',
                    //monthly_salary: '',
                    //gross_pay: '',
                    net_pay: '',
                    supervisor_name: '',
                    supervisor_email: '',
                    supervisor_phone: '',
                    supervisor_grade: '',
                    bankName: '',
                    bankCode: '',
                    accountNumber: '',
                    bvn: '',
                   
                },
                settings:{},
                employers: [],
                statesCopy: [],
                stateEmployers: [],
                section: 1,
                showEmployerModal: false,
                bankCodes: [],
                uploading:false,
                file: '',
                uploadPercentage: 0,
                fileLabels: [
                    'Letter of employment',
                    'Letter of Confirmation'
                ],
                dbFileLabels: [
                    'employment_letter',
                    'confirmation_letter',
                ],
                userCreated : false,
                fileNumber: 1,
                newEmploymentId: 0,
                addedUser: {},
                appointment: '',
                workId: '',
                validId: '',
                confirmation: '',
                passport: '',
                appForm: '',
                
                employerId: 0
            };
        },
        
        mounted() {
            this.initData(); 
        },
        
        methods: {
            initData() {
                this.employerStates.forEach(states => {
                    
                    states.state.split(',').forEach(state => {
                        
                        if (!this.statesCopy.includes(state)) {
                            this.statesCopy.push(state);
                        }
                        
                    });
                    
                });
                
                this.statesCopy = this.statesCopy.sort();
                
                this.allEmployers.forEach(employer => {
                    this.employers.push(employer); 
                });
                this.setStateEmployers();
                this.bankCodes = Object.keys(this.banks);
            },
            
            setStateEmployers() {
                this.stateEmployers = [];
                this.employers.forEach(employer => {
        
                    if(this.formData.employer_state && 
                        employer.state && employer.state.indexOf(this.formData.employer_state) !== -1) {
                        this.stateEmployers.push(employer);
                    } 
                });
            },

            onChangeEmployer(event) {

            if (this.formData.employer_id != '') {
                this.employers.forEach(employer =>{
                    if (employer.id == this.formData.employer_id && employer.id == event.target.value) {
                        this.settings = JSON.parse(employer.requireDocs);
                        this.employerId = employer.id;
                    }
                });
            }

            },
            
            async submitNewUserForm(e) {

               
                this.formData.bankName = this.banks[this.formData.bankCode];
                
                //TODO: validate data on front end
                
                if(this.formData.accountNumber == '' || this.formData.accountNumber.length != 10) {
                    alert('Please enter a valid account number');
                    return;
                }
                
                
                try {
                    this.startLoading();
                    const response = await axios.post(this.url, this.formData);
                    
                    this.alertSuccess(response.data.message + "");
                    this.clearFormData();
                    this.section = 1;
                    this.newEmploymentId = response.data.employment_id;
                    this.newUser = response.data.user;
                    this.section = 3;
                    this.stopLoading();
                    
                    
                } catch (e) {
                    this.stopLoading();
                    this.handleApiErrors(e);
                    
                }
               
            },
            async uploadDocument() {
                // if (this.file === '') return this.alertError("Please select a file");
                let msg;

                if (this.statement === '' && this.settings.bankStatement == true) {

                   let msg ='Please upload bank statement';
                }

                if (this.validId === '' && this.settings.validID == true) {
                   let msg ='Please provide valid ID';
                }

                if (this.workId === '' && this.settings.workID == true) {
                   let msg ='Please provide a work ID';
                }

                if (this.passport === '' && this.settings.passport == true) {

                   let msg ='Please provide a passport';
                }

                if (this.confirmation === '' && this.settings.passport == true) {
                   let msg ='Please provide a confirmation';
                }

                if (this.appointment === '' && this.settings.appLetter == true) {
                   let msg ='Please provide an appointment letter';
                }

                if (msg) {

                    this.alertError(msg+' '+ 'Document failed. Proceed to upload all documents');
                }
                
                    


                try {
                    
                    
                    let formData = new FormData();
                    // formData.append('file', this.file); 
                    // formData.append('label', this.dbFileLabels[this.fileNumber - 1]); 
                    formData.append('employment_id', this.newEmploymentId);
                    formData.append('appointment', this.appointment);
                    formData.append('workId', this.workId);
                    formData.append('passport', this.passport);
                    formData.append('confirmation', this.confirmation);
                    formData.append('validId', this.validId);
                    formData.append('appForm', this.appForm);
                    
                    this.uploading = true;
                    this.startLoading();

                    console.log(formData);
                
                    const response = await axios.post( this.uploadsUrl, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function( progressEvent ) {
                            this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
                        }.bind(this)
                    }).then((res)=> {
                        this.alertSuccess(res.data.message);
                        this.section  = 1;
                    });

                    
                                    
                                    
                    // this.file = '';
                    // this.$refs.file.value = "";
                    // //    update employment
                    // let employment = this.newUser.employments.find(employment => 
                    //     employment.id === response.data.employment.id);
                        
                    // if (employment) {
                    //     employment[this.dbFileLabels[this.fileNumber - 1]] = 
                    //         response.data.employment[this.dbFileLabels[this.fileNumber - 1]];
                    // }
                    
                    // this.fileNumber++;
                    
                    // if (this.fileNumber > 2) {
                    //     this.alertSuccess('Uploads Completed');
                    //     this.section = 1;
                    // }
                } catch(error) {
                    this.handleApiErrors(error);
                }

                 this.stopLoading();
                this.uploading = false;
                this.uploadPercentage = 0;
            },
            
            addNewEmployer(event) {
                this.employers.push(event.employer);
                
                if (!event.employer.state) return;
                
                event.employer.state.split(',').forEach(state => {
                    if (!this.statesCopy.includes(state)) {
                        this.statesCopy.push(state);
                    }
                    this.formData.employer_state = state;
                });
                
                this.formData.employer_id = event.employer.id;
                this.showEmployerModal = false;
            },
            
            clearFormData() {
                Object.keys(this.formData).forEach(key => this.formData[key] = '');
                this.formData.user_type = 1;
                this.formData.gender = 1;
                this.formData.marital_status = 1;
                this.employer_id = 0;
                this.no_of_children = 0;
            },

            //Upload scripts
            
            onUploadvalidId(e) {
                 this.validId = e.target.files[0];
            },

            onUploadworkId(e){
                this.workId = e.target.files[0];
            },

            onUploadconfirmation(e){
                this.confirmation = e.target.files[0];
            },

            onUploadpassport(e){
                this.passport = e.target.files[0];
            },

            onUploadappointment(e){
                 this.appointment = e.target.files[0];
            },

            onUploadappForm(e){
                 this.appForm = e.target.files[0];
            },
        
        
          
            async uploadDocument() {
                // if (this.file === '') return this.alertError("Please select a file");
                // if (this.confirmation === '' && this.settings.validID == true || this.statement === '' && this.settings.bankStatement == true || this.validId === '' && this.settings.validID == true || this.workId === '' && this.settings.workID || this.passport === '' && this.settings.passport == true || this.confirmation === '' && this.settings.confirmLetter || this.appointment === '' && this.appLetter == true) 
                //     return this.alertError("Please select a file");


                try {
                    
                    
                    let formData = new FormData();
                    // formData.append('file', this.file); 
                    // formData.append('label', this.dbFileLabels[this.fileNumber - 1]); 
                    formData.append('employment_id', this.newEmploymentId);
                    formData.append('appointment', this.appointment);
                    formData.append('workId', this.workId);
                    formData.append('passport', this.passport);
                    formData.append('confirmation', this.confirmation);
                    formData.append('validId', this.validId);
                    formData.append('appForm', this.appForm);
                    
                    this.uploading = true;
                    this.startLoading();
                
                    const response = await axios.post( this.uploadsUrl, formData, {
                                        headers: {
                                            'Content-Type': 'multipart/form-data'
                                        },
                                      
                                        onUploadProgress: function( progressEvent ) {
                                            this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
                                        }.bind(this)
                
                    
                                    });
                                    
                                    
                    // this.file = '';
                    // this.$refs.file.value = "";
                        // update employment
                    // let employment = this.newUser.employments.find(employment => 
                    //     employment.id === response.data.employment.id);
                        
                    // if (employment) {
                    //     employment[this.dbFileLabels[this.fileNumber - 1]] = 
                    //         response.data.employment[this.dbFileLabels[this.fileNumber - 1]];
                    // }
                    
                    // this.fileNumber++;
                    
                    // if (this.fileNumber > 2) {
                    //     this.alertSuccess('Uploads Completed');
                    //     this.section = 1;
                    // }
                    
                    this.stopLoading();
                    this.uploading = false;
                    this.uploadPercentage = 0;
                    
                
                } catch(error) {
                    this.handleApiErrors(error);
                    this.stopLoading();
                    this.uploading = false;
                    this.uploadPercentage = 0;
                }
            },
        },
        
        components: {
            'employer': Employer,
        },
        
        watch: {
            "formData.employer_state": function(current) {
                this.setStateEmployers();
            }
        }
    };
</script>
<style scoped>
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