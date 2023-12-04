<template>
    <div class="card">
        <div class="card-body">
            <form method="post" @submit.prevent="saveEmployer">
                <div class="row">
                    <div class="form-group col-sm-8">
                        <label class="control-label" for="employer_name"><strong>Employer Name</strong></label>
                        <input type="text" name="employer_name" id="employer_name"
                            class="form-control" required 
                            placeholder="Name of Employer" 
                            title="Name of Employer" v-model="employer.name">
                    </div>
                    
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="max_tenure"><strong>Max Monthly Tenure </strong></label>
                        <input type="number" name="max_tenure" id="max_tenure"
                            v-model="employer.max_tenure"
                            class="form-control" required >
                    </div>
                </div>
                            
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="rate_3"><strong>3 Months Rate</strong></label>
                        <input type="text" name="rate_3" id="rate_3" v-model="employer.rate_3"
                            class="form-control" required >
                    </div>
                        
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="rate_6"><strong>6 Months Rate</strong></label>
                        <input type="text" name="rate_6" id="rate_6" v-model="employer.rate_6"
                            class="form-control" required >
                    </div>
                        
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="rate_12"><strong>12 Months Rate</strong></label>
                        <input type="text" name="rate_12" id="rate_12" v-model="employer.rate_12"
                                class="form-control" required >
                    </div>
                </div>
                            
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="fee_3"><strong>3 Months Fees</strong></label>
                        <input type="text" name="fees_3" id="fee_3" v-model="employer.fees_3"
                            class="form-control" required >
                    </div>
                        
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="fee_6"><strong>6 Months Fees</strong></label>
                        <input type="text" name="fees_6" id="fee_6" v-model="employer.fees_6"
                            class="form-control" required >
                    </div>
                    
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="rate_12"><strong>12 Months Fee</strong></label>
                        <input type="text" name="fees_12" id="fee_12" v-model="employer.fees_12"
                            class="form-control" required >
                    </div>
                </div>

                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2">Weekly Repayment</legend>
                    <div class="row">
                        <div class="row col-sm-12">
                            <div class="form-group col-sm-4">
                                <label class="control-label" for="max_weekly_tenure"><strong>Max Weekly Tenure</strong></label>
                                <input type="number" name="max_weekly_tenure" id="max_weekly_tenure"
                                    v-model="employer.max_weekly_tenure"
                                    class="form-control" required >
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="has_weekly_repayment"><strong>Allow Weekly Repayment</strong></label>
                                <input type="checkbox" name="has_weekly_repayment" id="has_weekly_repayment" class="form-control" 
                                v-model="employer.has_weekly_repayment"
                                >
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="weekly_rate_3"><strong>3 Weeks Rate</strong></label>
                            <input type="text" name="weekly_rate_3" id="weekly_rate_3" v-model="employer.weekly_rate_3"
                                class="form-control" required >
                        </div>
                            
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="weekly_rate_6"><strong>6 Weeks Rate</strong></label>
                            <input type="text" name="weekly_rate_6" id="weekly_rate_6" v-model="employer.weekly_rate_6"
                                class="form-control" required >
                        </div>
                            
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="weekly_rate_12"><strong>12 Weeks Rate</strong></label>
                            <input type="text" name="weekly_rate_12" id="weekly_rate_12" v-model="employer.weekly_rate_12"
                                    class="form-control" required >
                        </div>
                    </div>
                                
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="weekly_fees_6"><strong>3 Weeks Fees</strong></label>
                            <input type="text" name="weekly_fees_6" id="weekly_fees_6" v-model="employer.weekly_fees_3"
                                class="form-control" required >
                        </div>
                            
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="weekly_fees_6"><strong>6 Weeks Fees</strong></label>
                            <input type="text" name="weekly_fees_6" id="weekly_fees_6" v-model="employer.weekly_fees_6"
                                class="form-control" required >
                        </div>
                        
                        <div class="form-group col-sm-4">
                            <label class="control-label" for="weekly_fees_12"><strong>12 Weeks Fee</strong></label>
                            <input type="text" name="weekly_fees_12" id="weekly_fees_12" v-model="employer.weekly_fees_12"
                                class="form-control" required >
                        </div>
                    </div>
                </fieldset>
                            
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="employer_email"><strong>Employer Email</strong></label>
                        <input type="email" name="employer_email" id="employer_email"
                            class="form-control" required 
                            placeholder="Email of Employer" title="Email of Employer"
                            v-model="employer.email">
                    </div>
                    
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="employer_phone"><strong>Employer Phone</strong></label>
                        <input type="number" name="employer_phone" id="employer_phone"
                            class="form-control" required 
                            placeholder="Enter the phone number of employer"
                            title="Phone number of employer"
                            v-model="employer.phone">
                    </div>
                    
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="employer_address"><strong>Employer Address</strong></label>
                        <input type="text" name="employer_address" id="employer_address"
                            class="form-control" required 
                            placeholder="Address of Employer" title="Address of Employer"
                            v-model="employer.address">
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="control-label" for="vat_fee"><strong>Collection VAT Fee</strong></label>
                        <input type="number" name="vat_fee" id="vat_fee"
                            class="form-control"  
                            placeholder="VAT Fee" title="VAT Fee"
                            v-model="employer.vat_fee">
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="loan_vat_fee"><strong>Loan VAT Fee</strong></label>
                        <input type="number" name="loan_vat_fee" id="loan_vat_fee"
                            class="form-control"  
                            placeholder="Loan VAT Fee" title="Loan VAT Fee"
                            v-model="employer.loan_vat_fee">
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="interest_vat_fee"><strong>Interest VAT Fee</strong></label>
                        <input type="number" name="interest_vat_fee" id="interest_vat_fee"
                            class="form-control"  
                            placeholder="Interest VAT Fee" title="Interest VAT Fee"
                            v-model="employer.interest_vat_fee">
                    </div>

                </div>
                <br/>                   
                <div class="row col-sm-12">
                    <div class="form-group">
                        <label class="control-label" for="employer_state">
                            <strong>Employer States</strong>&nbsp;&nbsp;
                            <button type="button" class="btn btn-xs btn-primary" @click="startStateSelection">
                                <i class="fa fa-cogs"></i>
                                Select States
                            </button>
                        </label>
                        <div class="row statesBoard" v-if="selectingStates">
                           <div class="col-sm-3 item" v-for="state in states">
                                <input class="state-item" :data-state="state" type="checkbox" /> {{ state }}
                            </div>
                            <br/><br/>
                            <div class="text-right">
                                <button type="button" class="btn btn-success btn-xs" @click="finishStateSelection">
                                    <i class="fa fa-check"></i>
                                    Done
                                </button>
                            </div>
                        </div>
                        <div v-else-if="employer.state">
                            <strong><em><small>Selected States:</small></em></strong> 
                            &nbsp;{{ employer.state }}
                        </div>
                    </div>
                </div>
                <br/>  
                 <div class="row col-sm-6">
                     <div class="form-group">
                         <label for="">Choose Employer status</label>
                         <select v-model="employer.is_primary" class="form-control" name="status">
                             <option value="0">Primary</option>
                             <option value="1">Secondary</option>
                             <option value="2">Merchant</option>
                         </select>
                         
                   
                     </div>
                     
                 </div>                   
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="payment_date"><strong>Date of Salary Payment</strong></label>
                        <input type="text" name="payment_date" 
                            class="form-control" required 
                            placeholder="DD/MM" v-model="employer.payment_date">
                    </div>
                    
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="payment_mode"><strong>Payment Mode</strong></label>
                        <select name="payment_mode" class="form-control" v-model="employer.payment_mode"> 
                            <option value="1">Bank Transfer</option>
                            <option value="2">E-Transfer</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="employee_count"><strong>Number of Staff</strong></label>
                        <input type="number" name="employee_count"
                            class="form-control" v-model="employer.employee_count"
                            placeholder="Number of Staff">
                    </div>
                </div>
                                    
                <div class="row">
                                
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="approver_name"><strong>Approver's Name</strong></label>
                        <input type="text" name="approver_name"
                            class="form-control" v-model="employer.approver_name"
                            placeholder="Name of Approving Officer">
                    </div>
                    
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="approver_designation"><strong>Designated Office</strong></label>
                        <input type="text" name="approver_designation"
                            class="form-control" v-model="employer.approver_designation"
                            placeholder="Designated office of Approver">
                    </div>
                    
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="approver_phone"><strong>Phone Number</strong></label>
                        <input type="number" name="approver_phone"
                            class="form-control"  v-model="employer.approver_phone"
                            placeholder="Phone Number">
                    </div>
                    
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="approver_email"><strong>Email Address</strong></label>
                        <input type="email" name="approver_email"
                            class="form-control" v-model="employer.approver_email" 
                            placeholder="Contact Email Address">
                    </div>
                    
                    <div class="form-group col-sm-4">
                        <label class="control-label" for="verified_employer"><strong>Verification Status</strong></label>
                        <select name="verified_employer" class="form-control" v-model="employer.is_verified"> 
                            <option value="0">Unverified</option>
                            <option value="1">Under Verification</option>
                            <option value="2">Verification Denied</option>
                            <option value="3">Verified</option>
                        </select>
                    </div>
                </div>
                <br/><br/>
                <div class="row">
                    <div class="col-sm-12">
                        <h4>Collection Methods</h4>
                    </div>
                    <br/>
                    <div class="col-sm-6">
                        <h5 class="badge badge-primary">Primary</h5>
                        <br/><br/>
                        <div class="form-group">
                            <label>Select Type</label>
                            <select class="form-control" v-model="primaryType">
                                <option v-for="key in Object.keys(collectionPlans || {})"
                                    :value="key" :key="`primary-${key}`">
                                    {{ key.toUpperCase() }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Method</label>
                            <select class="form-control" v-model="employer.collection_plan" required>
                                <option v-for="key in Object.keys(selectedPrimaryTypes)"
                                    :key="`primary-${key}`"
                                    :value="key">
                                    {{ selectedPrimaryTypes[key] }}
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-sm-6">
                        <h5 class="badge badge-danger">Secondary</h5>
                        <br/><br/>
                        <div class="form-group">
                            <label>Select Type</label>
                            <select class="form-control" v-model="secondaryType">
                                <option v-for="key in Object.keys(collectionPlans || {})" 
                                    :value="key" :key="`secondary-${key}`">
                                    {{ key.toUpperCase() }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Method</label>
                            <select class="form-control" v-model="employer.collection_plan_secondary" required>
                                <option v-for="key in Object.keys(selectedSecondaryTypes)"
                                    :key="`secondary-${key}`"
                                    :value="key">
                                    {{ selectedSecondaryTypes[key] }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                    
                <div class="text-right">
                    <button type="submit" class="btn btn-success">
                        <i :class="spinClass"></i>
                        Save
                    </button>
                </div>            
    
            </form>
        </div>
    </div>
</template>
<script>
    import { utilitiesMixin } from '../../mixins';
    
    export default {
        props: ["states", "collectionPlans", "existingEmployer","primaryEmployers"],
        
        mixins: [utilitiesMixin],
        
        data() {
            return {
                employer: {},
                selectingStates: false,
                primaryType: '',
                secondaryType: '',
                selectedPrimaryTypes: {},
                selectedSecondaryTypes: {},
                
            };
        },
        
        mounted() {
            // Copy over if there's an existing employer
            if (this.existingEmployer) {
                this.primaryType = 
                    Object.keys(this.collectionPlans)
                    [Number(`${this.existingEmployer.collection_plan}`.substring(0,1)) - 1];
                
                this.secondaryType = 
                    Object.keys(this.collectionPlans)
                    [Number(`${this.existingEmployer.collection_plan_secondary}`.substring(0,1)) - 1];
                
                this.employer = JSON.parse(JSON.stringify(this.existingEmployer));
            }
                
        },
        
        methods: {
            async saveEmployer() {
                if (this.employer.id) {
                    await this.updateEmployer();
                } else {
                    await this.createEmployer();
                }
            },
            
            async createEmployer() {
                this.startLoading();
                
                try {
                    
                    const response = await axios.post('/ucnull/employers', this.employer);
                    
                    this.alertSuccess(
                        `Success! <a href="/ucnull/employers/view/${response.data.employer.id}">View Employer →</a>`
                    );
                    
                    this.employer = {};
                    
                    this.stopLoading();
                    
                } catch(e) {
                    this.handleApiErrors(e);
                    this.stopLoading();
                }
            },
            
            async updateEmployer() {
                this.startLoading();
                
                try {
                    
                    const response = await axios.post(`/ucnull/employers/${this.employer.id}`, this.employer);
                    
                    this.alertSuccess(
                        `Update Successful! <a href="/ucnull/employers/view/${response.data.employer.id}">View Employer →</a>`
                    );
                    
                    this.stopLoading();
                    
                } catch(e) {
                    this.handleApiErrors(e);
                    this.stopLoading();
                }
            },
            
            
            startStateSelection() {
                this.selectingStates = true;
                
                const states = this.employer.state ? this.employer.state.split(',') : [];
                
                setTimeout(() => {
                    
                    document.querySelectorAll('.state-item').forEach(input => {
                        console.log(states.includes(input.getAttribute('data-state')));
                        if (states.includes(input.getAttribute('data-state'))) {
                            input.checked = true;
                        }
                    });
                    
                }, 100);
                
                
            },
            
            finishStateSelection() {
                const states = [];
                
                document.querySelectorAll('.state-item').forEach(input => {
                    if (input.checked) {
                        states.push(input.getAttribute('data-state'));
                    } 
                });
                
                this.employer.state = states.join(',');
                
                this.selectingStates = false;
                
            },
        },
        
        watch: {
            "primaryType": function (value) {
                const options = this.collectionPlans[value];
                
                this.selectedPrimaryTypes = {};
                
                if (options) {
                    Object.keys(options).forEach(key => {
                        this.selectedPrimaryTypes[key] = options[key];
                    })
                }
            },
            
            "secondaryType": function (value) {
                const options = this.collectionPlans[value];
                
                this.selectedSecondaryTypes = {};
                
                if (options) {
                    Object.keys(options).forEach(key => {
                        this.selectedSecondaryTypes[key] = options[key];
                    })
                }
            }
        }
    };
</script>

<style scoped>
    
    .statesBoard {
        box-shadow: 1px 1px 4px grey;
        border-radius: 10px;
        padding: 2em 1.5em;
    }
    
    .statesBoard .item {
        margin-bottom: 20px;
    }
</style>