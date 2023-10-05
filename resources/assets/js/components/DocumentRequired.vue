<template>
	<div class="row">
       <div class="col-md-12">
           <div class="card">
             <h5 class="card-header">Documents required</h5>
               <div class="card-body">
                 <form class="form" method="post">

                    <div class="form-group">
                        <label for="workID">Work ID</label>
                        <input type="checkbox" name="workID" id="workID" class="form-control" v-model="settings.workID">
                    </div>

                    <div class="form-group">
                        <label for="validID">Valid ID</label>
                        <input type="checkbox" name="validID" id="validID" checked class="form-control" v-model="settings.validID">
                    </div>

                    <div class="form-group">
                        <label for="payrollID">Payroll ID</label>
                        <input type="checkbox" name="payrollID" id="payrollID" class="form-control" v-model="settings.payrollID">
                    </div>

                    <div class="form-group">
                        <label for="appLetter">Appointment letter</label>
                        <input type="checkbox" name="appLetter" id="appLetter" class="form-control" v-model="settings.appLetter">
                    </div>

                    <div class="form-group">
                        <label for="confirmLetter">Confirmation letter</label>
                        <input type="checkbox" name="confirmLetter" id="confirmLetter" class="form-control" v-model="settings.confirmLetter">
                    </div>

                    <div class="form-group">
                        <label for="passport">Passport</label>
                        <input type="checkbox" name="passport" id="passport" class="form-control" v-model="settings.passport">
                    </div>

                     <div class="form-group">
                    	<button type="button" class="btn btn-success float-right" :disabled="loading" v-on:click="submitForm($event)"><i :class="buttonClass"></i>Save</button>
                    </div>
                 </form>
               </div>
           </div>
       </div>
   </div>
</template>

<script>
import { utilitiesMixin } from "../mixins";

    export default {
		props:['user_id','settings'],

        mixins: [utilitiesMixin],

		data() {
		  return{
            toggle:[],
            selected:[],
            loading: false,
            buttonClass: {
                fa: true,
                "fa-check-circle-o": true,
                "fa-spin": false,
                "fa-spinner": false
            },
          };
			
		},
		componetDidMount(){
			// console.log("hello componetDidMount");
		},
		created(){
	
        },
		methods:{
			submitForm: function(event){
                 this.startLoading();
			 	axios.post('/ucnull/employers/documents/required/'+this.user_id, this.settings)
                .then(response => {
					// console.log(response);
                    this.stopLoading();
                    this.alertSuccess("Settings  updated");
				}).catch(error => {
                    this.stopLoading();
                    this.alertError(error);
				})
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
            }
		},
	};
</script>
