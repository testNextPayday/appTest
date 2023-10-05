<template>
    <div>
        <modal v-if="showModal" @close="closeModal">
            <div slot="header">
                <h4>Add Employer
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="closeModal">&times;</button>
                    </span>
                </h4>
            </div>
    
            <div slot="body">
                <form method="POST" action="#" @submit.prevent="saveEmployer" id="newEmployerForm">
                    <div v-if="page === 1">
                        <div class="form-group">
                            <label class="control-label" for="employer_name"><strong>Employer Name</strong></label>
                            <input type="text" class="form-control" id="employer_name" name="employer_name" required v-model="employer.name"
                                placeholder="Name of Employer" title="Name of your Employer">
                        </div>`
                        
                        <div class="form-group">
                            <label class="control-label" for="employer_email"><strong>Employer Email</strong></label>
                            <input type="email" name="employer_email" id="employer_email"
                                class="form-control" required v-model="employer.email"
                                placeholder="Email of Employer" title="Email of your Employer">
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="employer_phone"><strong>Employer Phone</strong></label>
                            <input type="number" name="employer_phone" id="employer_phone"
                                class="form-control" required v-model="employer.phone"
                                placeholder="Enter the phone number of your employer" title="Phone number of employer">
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="employer_address"><strong>Employer Address</strong></label>
                            <input type="text" name="employer_address" id="employer_address"
                                class="form-control" required v-model="employer.address"
                                placeholder="Address of Employer" title="Address of Employer">
                        </div>
                    </div>
                    
                    <div v-if="page === 2">
                        <div class="form-group">
                            <label class="control-label" for="employer_state">
                                <strong>Employer State</strong>
                                <button type="button" class="btn btn-xs btn-primary"
                                    @click="startAddingStates">Select States</button>
                            </label>
                            <div>
                                {{ employer.state }}
                            </div>
                        </div>
                        
                    </div>
                    
                    <!-- <div v-else>
                        <div class="form-group">
                            <label class="control-label" for="payment_date"><strong>Payment Date (DD/MM)</strong></label>
                            <input type="text" name="payment_date" id="payment_date"
                                class="form-control" required v-model="employer.payment_date"
                                placeholder="When is payment made (DD/MM)" title="When do you get paid">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="payment_mode"><strong>Mode of Payment</strong></label>
                            <select class="form-control" name="payment_mode" id="payment_mode" v-model="employer.payment_mode" required>
                                <option disabled>Choose mode of payment</option>
                                <option value="1">Bank Transfer</option>
                                <option value="2">E-Transfer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="approver_name"><strong>Approving Officer's Name</strong></label>
                            <input type="text" name="approver_name" id="approver_name"
                                class="form-control" required v-model="employer.approver_name"
                                placeholder="Approving Officer's Name" title="Approving Officer's Name">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="approver_designation"><strong>Approving Officer's Designation</strong></label>
                            <input type="text" name="approver_designation" id="approver_designation"
                                class="form-control" required v-model="employer.approver_designation"
                                placeholder="Approving Officer's Designation" title="Approving Officer's Designation">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="approver_email"><strong>Approving Officer's Email</strong></label>
                            <input type="email" name="approver_email" id="approver_email"
                                class="form-control" required v-model="employer.approver_email"
                                placeholder="Approving Officer's Email Address" title="Approving Officer's Email Address">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="approver_phone"><strong>Approving Officer's Phone</strong></label>
                            <input type="number" name="approver_phone" id="approver_phone"
                                class="form-control" required v-model="employer.approver_phone"
                                placeholder="Approving Officer's Phone Number" title="Approving Officer's Phone Number">
                        </div>
                    </div>-->
                </form>     
            </div>
    
            <div slot="footer">
                <div>
                    <button type="button" class="btn btn-primary" v-if="page !== 1" @click="--page" :disabled="loading">
                        <i class="fa fa-arrow-left"></i>
                        Prev
                    </button>
                    <button type="button" class="btn btn-primary pull-right" v-if="page !== 2" @click="page++" :disabled="loading">
                        <i class="fa fa-arrow-right"></i>
                        Next
                    </button>
                    <button type="submit" form="newEmployerForm" class="btn btn-success pull-right" v-if="page === 2" :disabled="loading">
                        <i :class="spinClass"></i>
                        Save Employer
                    </button>
                </div>
            </div>
        </modal>
        
        
        <modal v-if="addingStates" @close="addingStates = false">
            <div slot="header">
                <h4>Select Employer States
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="finishAddingStates">&times;</button>
                    </span>
                </h4>
            </div>
    
            <div slot="body">
                <div class="row statesBoard">
                   <div class="col-sm-12 item" v-for="state in states">
                        <input class="state-item" :data-state="state" type="checkbox" /> {{ state }}
                    </div>
                </div>
            </div>
    
            <div slot="footer">
                <div>
                    <button type="button" class="btn btn-primary" @click="finishAddingStates">
                        <i class="fa fa-check-circle-o"></i>
                        Done
                    </button>
                </div>
            </div>
        </modal>
    </div>
    
</template>
<script>
    import { utilitiesMixin } from '../mixins';
    
    export default {
        props: ["currentState", "show", "url", "states"],
        
        mixins: [utilitiesMixin],
        
        data() {
            return {
                employer: {
                    payment_date: '',
                    payment_mode: '',
                    name: '',
                    email: '',
                    phone: '',
                    address: '',
                    state: '',
                    approver_name: '',
                    approver_email: '',
                    approver_phone: '',
                    approver_designation: '',
                },
                page: 1,
                showModal: false,
                addingStates: false
            };
        },
        
        methods: {
            closeModal() {
                this.$emit('modal-closed');
            },
            
            finishAddingStates() {
                const states = [];
                
                document.querySelectorAll('.state-item').forEach(input => {
                    if (input.checked) {
                        states.push(input.getAttribute('data-state'));
                    } 
                });
                
                this.employer.state = states.join(',');
                
                this.addingStates = false;
            },
            
            startAddingStates() {
                this.addingStates = true;  
                
                const states = this.employer.state ? this.employer.state.split(',') : [];
                
                setTimeout(() => {
                    
                    document.querySelectorAll('.state-item').forEach(input => {
                        if (states.includes(input.getAttribute('data-state'))) {
                            input.checked = true;
                        }
                    });
                    
                }, 100);
            },
            
            async saveEmployer() {
                let dataIsValid = this.validateData();
                if(!dataIsValid) return;
                
                try {
                    this.startLoading();
                    
                    const response = await axios.post(this.url, this.employer);
                    
                    this.$emit('employer-added', response.data);  
                    alert('Employer Added Succesfully, Please Wait for Admin Approval')      
                    this.closeModal();
                    this.stopLoading();                    
                   
                } catch (e) {
                    this.stopLoading();
                    this.handleApiErrors(e);
                }
                
            },
            
            validateData() {
                const errors = [];
                
                // Page 1 errors
                if(this.employer.name == '') {
                    errors.push("Please enter a valid Employer's Name");
                }
                
                if(this.employer.email == '' || !this.validateEmail(this.employer.email)) {
                    errors.push("Please enter a valid Employer's Email");
                }
                
                if(this.employer.phone == '') {
                    errors.push("Please enter a valid Employer's Phone Number");
                }
                
                if(this.employer.address == '') {
                    errors.push("Please enter a valid Employer's Address");
                }
                
                if (errors.length) {
                    this.errorBag = errors;
                    this.displayErrors();
                    this.switchPage(1);
                    return false;
                }
                
                // Page 2 errors
                if(this.employer.state == '') {
                    errors.push("Please enter a valid Employer's State");
                }
                
                // if(this.employer.payment_date == '') {
                //     errors.push("Please enter a valid Payment Date");
                // }
                
                // if(this.employer.payment_mode == '') {
                //     errors.push("Please enter a valid Payment Mode");
                // }
                
                if (errors.length) {
                    this.errorBag = errors;
                    this.displayErrors();
                    this.switchPage(2);
                    return false;
                }
                
                // Page 3 errors

                // if(this.employer.approver_name == '') {
                //     errors.push("Please enter a valid Approving Officer's Name");
                // }
                
                // if(this.employer.approver_email == '' || !this.validateEmail(this.employer.approver_email)) {
                //     errors.push("Please enter a valid Approving Officer's Email");
                // }
                
                // if(this.employer.approver_phone == '') {
                //     errors.push("Please enter a valid Approving Officer's Phone Number");
                // }
                
                // if(this.employer.approver_designation == '') {
                //     errors.push("Please enter a valid Approving Officer's Designation");
                // }
                
                if (errors.length) {
                    this.errorBag = errors;
                    this.displayErrors();
                    this.switchPage(2);
                    return false;
                }
                
                return true;
            },
            
            switchPage(number) {
                this.page = number;  
            },
            
            closeModal() {
                this.showModal = false;
                this.$emit('modal-closed', false);
            },
        },
        
        watch: {
            "currentState": function(currentState) {
                this.employer.state = currentState;
            },
            "show": function(current) {
                this.showModal = current;
            }
        }
    };
</script>
<style scoped>
    .statesBoard {
        max-height: 60vh;
        overflow: auto;
    }
</style>