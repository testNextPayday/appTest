<template>
    <div>
        <div>
            <p class="text-right">
                <button class="btn btn-sm btn-primary" @click="addOfficer">
                    <i class="fa fa-plus"></i>
                    Add New
                </button>
            </p>
        </div>
        <hr/>
        <table class="table table-responsive-sm table-hover table-outline mb-0">
            <thead class="thead-light">
                <tr>
                    <th class="text-center"><i class="icon-user"></i></th>
                    <th>Name</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Phone</th>
                    <th class="text-center">Position</th>
                    <th class="text-center">Manage</th>
                </tr>
            </thead>
            
            <tbody>
                <tr v-if="officers.length < 1">
                    <td colspan="6" class="text-center">
                        You have not specified your key officers information
                    </td>
                </tr>
                <tr v-for="(officer, i) in officers">
                    <td class="text-center"> {{i + 1}}</td>
                    <td><div> {{officer.name}}</div></td>
                    <td class="text-center">
                        <div class="small text-muted">
                            {{officer.email}} 
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="small text-muted">
                            {{officer.phone}}
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="small text-muted">
                            {{officer.position}}
                        </div>
                    </td>
                    <td class="text-center">
                        <div>
                            <button class="btn btn-sm btn-primary" @click="editOfficer(officer.id)" :disabled="loading">
                                <i class="fa fa-edit"></i> Edit
                            </button>&nbsp;
                            <button class="btn btn-sm btn-danger" @click="deleteOfficer(officer.id)" :disabled="loading">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>{{mode === 'new' ? 'Add key officer' : 'Update Officer Information'}}
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div>
                    <h5 class="text-danger text-center">
                         {{errorMessage}}   
                    </h5>
                    
                    <ul v-if="errorBag.length > 0">
                        <li v-for="error in errorBag" class="text-danger">{{error}}</li>
                    </ul>
                    
                    <form method="post" @submit.prevent="handleSubmission">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" v-model="officer.name" required/>
                        </div>
                        
                        <div class="form-group">
                            <label for="name">Email</label>
                            <input type="email" class="form-control" name="email" v-model="officer.email" required/>
                        </div>
                        
                        <div class="form-group">
                            <label for="name">Phone</label>
                            <input type="text" class="form-control" max="11" name="phone" v-model="officer.phone" required/>
                        </div>
                        
                        <div class="form-group">
                            <label for="name">Position</label>
                            <input type="text" class="form-control" name="position" v-model="officer.position" required/>
                        </div>
                        
                        <button class="btn btn-info" :disabled="loading">
                            <i :class="spinClass"></i>
                            {{mode === 'new' ? 'Add officer' : 'Update officer'}}
                        </button>
                        
                    </form>
                    
                 
                </div>
            </div>

            <div slot="footer">
                <div></div>
            </div>
        </modal>
    </div>
</template>
<script>
    import { utilitiesMixin } from './../../mixins';
    
    export default {
        props: ['keyOfficers'],
        
        mixins: [utilitiesMixin],
        
        data() {
            return {
                officers: [],
                mode: 'new',
                editing: 0,
                showModal: false,
                officer: {}
            };
        },
        
        mounted() {
            this.officers = JSON.parse(JSON.stringify(this.keyOfficers));  
            console.log(typeof this.officers);
        },
        
        methods: {
            
            handleSubmission() {
                if (this.mode === 'new') {
                    this.saveOfficer();
                }  else {
                    this.updateOfficer();
                }
            },
            
            addOfficer() {
                this.mode = 'new'; 
                this.showModal = true;
                this.officer = {};
            },
            
            saveOfficer() {
                this.startLoading();
                if (this.officer.id) delete this.officer.id;
                
                axios.post('/lenders/officers/store', this.officer).then(response => {
                    if (response.data.status === 1) {
                        this.officers.push(response.data.officer);
                        this.showModal = false;
                        this.alertSuccess(response.data.message);
                    } else {
                        this.alertError(response.data.message);
                    }
                    this.stopLoading();
                }).catch(this.errorHandler);
            },
            
            editOfficer(id) {
                let officer = this.officers.find((_officer) => _officer.id === id);
                this.editing = officer.id;
                this.mode = 'edit';
                Object.keys(officer).forEach((key) => {
                    this.officer[key] = officer[key];
                });
                this.showModal = true;
            },
            
            updateOfficer() {
                this.startLoading();
                this.officer.id = this.editing;
                
                axios.post('/lenders/officers/update', this.officer).then(response => {
                    if (response.data.status === 1) {
                        let officer = this.officers.find(_officer => response.data.officer.id === _officer.id);
                        
                        if (officer) {
                            Object.keys(officer).forEach(key => {
                               officer[key] = response.data.officer[key]; 
                            });
                        }
                        
                        this.showModal = false;
                        this.alertSuccess(response.data.message);
                    } else {
                        this.alertError(response.data.message);
                    }
                    this.stopLoading();
                }).catch(this.errorHandler);
            },
            
            deleteOfficer(id) {
                let proceed = confirm('Are you absolutely sure you want to do this? This action cannot be reversed');
                if (!proceed) return;
                
                axios.get(`/lenders/officers/delete/${id}`).then(response => {
                    if (response.data.status === 1) {
                        let officer = this.officers.find(_officer => id === _officer.id);
                        let index = this.officers.indexOf(officer);
                        
                        if (index !== -1) {
                            this.officers.splice(index, 1);
                            this.alertSuccess(response.data.message);
                        }
                        
                    } else {
                        this.alertError(response.data.message);
                    }
                    this.stopLoading();
                }).catch(this.errorHandler);
            },
            
            errorHandler(error) {
                if(error.response.status === 422) {
                    this.errorMessage = error.response.data.message;
                    let errorMessages = Object.keys(error.response.data.errors);
                    errorMessages.forEach((errorKey) => {
                        this.errorBag.push(error.response.data.errors[errorKey][0]);
                    });
                } else {
                    this.errorMessage = error.message;
                }
                this.stopLoading();
            }
        }
    };
</script>