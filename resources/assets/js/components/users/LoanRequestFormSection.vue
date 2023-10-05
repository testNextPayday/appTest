<template>
    <div>

      <div class="row" v-if="uploading || verifying" style="margin-top:5rem;">
         <staggered-loader :msg="'Processing. Please wait'"></staggered-loader>
      </div>

      <div v-else>

        <div class="card">
          <div class="card-header">
            <strong>Loan</strong>
            <small>Request</small>        
            <span class="pull-right">Verification Fee: <b>₦ {{ application_fee2 < 1 ? application_fee : application_fee2}} </b></span>
          </div>
            <div class="card-body" >
              <div class="form-group" v-if="request">
               
                <incomplete-loan-request-form :request="request"></incomplete-loan-request-form>

              </div>

              <div class="form-group">
                  <label for="company">Select Employment</label>
                  <select class="form-control selectpicker" id="employment" name="employment_id" v-model="employment_id" data-live-search="true" @change="onChangeEmployer" required>
                      <option v-for="(employment, index) in employments" :key="index" :value="employment.id" >
                          {{employment.position}} - {{employment.employer.name}}
                      </option>
                  </select>
              </div>


              <template v-if="(loandocs.bank_statement == true && bankstatementcleared == false)">                
                    <!-- If enable mono is turned on use mono-->
                    <template>
                      <div id="statementButton">
                        <p>Click one of the Following to Retrieve Bank Statement</p>                        
                        <div class="d-flex justify-content-between">
                          <button @click.prevent='mbscall' type="button" class="btn btn-success"> MBS </button>
                          <button @click.prevent='monocall' type="button"  class="btn btn-info"> Mono </button>                        
                        </div>
                      </div>
                    </template>

                    <template v-if="enableMono">
                        <mono-statement :reference="user" :monokey="monokey" @cleared="showStatementMethod"></mono-statement>
                    </template>

                    <template v-if="enableMbs">
                        <my-bank-statement :user="user.reference" @cleared="showStatementMethod"></my-bank-statement>
                    </template>

              </template>

              <!-- When retrieving statement from old request just show statement -->
              <template v-if="statement">
                <div class="alert alert-success"> <a :href="statement" target="_blank"><i class="fa fa-file-pdf-o"></i></a>  Statement has been retrieved proceed to book loan  </div>
              </template>

              <template v-if="(loandocs.bank_statement == true && bankstatementcleared == true) || (loandocs.bank_statement == false)">
                    
                  <div class="form-group">
                      <label for="duration" style="display:block">Loan Duration (Months) <span class="pull-right">Max: <strong>{{max_duration}}</strong></span></label>
                      <input type="number" class="form-control" name="duration" @blur="confirmAmount" v-on:keyup="confirmAmount" v-model="duration" placeholder="How long do you want this loan for" required>
                  </div>

                  <div class="form-group" style="display:none;">
                      <label for="interest_percentage">Interest Percentage <small><em>(This is determined by your selected employer)</em></small></label>
                      <input min="1" max="100" type="number" v-model="interest_percentage" id="interest_percentage" class="form-control"
                          name="interest_percentage" placeholder="How many percent return?" readonly required>
                  </div>

                  <div class="form-group">
                      <label for="company" style="display:block">Amount Needed <span class="pull-right">Max: <b>{{max_amount}}</b></span></label>
                      <input type="number" class="form-control" id="amount" v-model="amount" name="amount" required
                          placeholder="Enter an amount in Naira" @blur="confirmAmount" v-on:keyup="confirmAmount" @change="confirmAmount">
                  </div>

                  <input type="hidden" name="newAmount" v-model="newAmount">

                  <input type="hidden" name="charge" v-model="charge">

                  <div>
                      <p class="mb-2 mt-2" style="font-size:0.9rem;">
                          <i class="icon-check text-success"></i> Gross loan
                          <span>
                              ₦{{grossLoan == 0 ? '0' : grossLoan.toLocaleString('en', {maximumFractionDigits: 2}) }}
                          </span>
                      </p>      
                  </div>

                  <div class="">
                      <p class="mb-2 mt-2" style="font-size:0.9rem;">
                          <i class="icon-check text-success"></i> Loan fee
                          <span>
                              ₦{{charge == 0 ? '0' : charge.toLocaleString('en', {maximumFractionDigits: 2}) }} 
                          </span>
                      </p>
                  </div>

                  <div class="">
                      <p class="mb-3 mt-2" style="font-size:0.9rem;">
                        <i class="icon-check text-success"></i> Disbursal amount
                        <span>
                          ₦{{disbursal == 0 ? '0' : disbursal.toLocaleString('en', {maximumFractionDigits: 2}) }}
                        </span> 
                      </p>
                  </div>

                  <div class="row mb-2" v-if="shouldCapitalize">
                    <div class="col">
                      <div class="float-right font-weight-bold">
                        <label class="active">
                          <input type="radio" value="1" v-model="setoff" required @change="confirmAmount"> Set-off
                        </label>

                        <label class="">
                          <input type="radio" value="0" v-model="setoff" required @change="confirmAmount"> Capitalize 
                        </label>
                      </div>
                    </div>
                  </div>
                      
                  <hr/>
                    <p>Monthly Repayment: <strong>{{emi}}</strong></p>
                    <small style="color:cadetblue">Loan charged @ {{interest_percentage}}%</small>
                  <hr/>

                  <!-- <hr/>
                    <p>Total Repayment Fee: <strong>{{total_repayment_fee}}</strong></p>
                    <small style="color:cadetblue">Loan charged @ {{interest_percentage}}%</small>
                  <hr/> -->

                  <!-- <div class="form-group">
                    <label for="referrer"> 
                      <b>Pick a Referrer &nbsp;&nbsp; </b>
                    </label>
                    <label class="active">
                      <input type="radio" name="referral_method" value="1" v-model="referralMethod"> Enter Affiliate Code
                    </label>
                    
                    <label class="">
                      <input type="radio" name="referral_method" value="2" v-model="referralMethod"> Select a borrower
                    </label>
                  </div> -->
                  
                  <div class="form-group" v-if="referralMethod == 1">
                    <label for="aff_code" style="display:block">Affiliate Code <small><i>(Not Required)</i></small></label>
                    <input type="text" class="form-control" name="code" v-model="formData.code" >
                  </div>

                  <div class="form-group" v-else-if="referralMethod == 2">
                    <label for="reference">Enter Borrower Phone</label>
                    <input type="text" class="form-control" name="code" v-model="formData.code" >
                  </div>

                  <div v-else>
                      <label>No affiliate method selected</label>
                  </div>

                  <!-- <div class="row">
                    <div class="form-group col-sm-12">
                      <label for="textarea-input">Purpose of Loan</label>
                      <textarea id="textarea-input" name="comment" v-model="formData.comment" rows="3" class="form-control" placeholder="This is your opportunity to increase your chances of getting a Loan" required>
                      </textarea>
                    </div>  
                  </div> -->

                  <div class="checkbox">
                    <label for="checkbox2">
                      <input type="checkbox" id="checkbox2" name="accept_insurance" required> I agree to the <a href="http://nextpayday.ng/terms/loan/" target="_blank">terms and conditions</a> of Nextpayday.
                    </label>
                  </div>

                  <!-- <label class="text-danger">Please ensure your card will not expire before proceeding</label> -->
                      
                  <div class="mb-2 float-right mr-3">
                    <button type="submit" class="btn btn-primary" @click.prevent="payWithPaystack" id="booking-form"><i class="fa fa-dot-circle-o"></i> Submit</button>
                  </div>

              </template>
            </div>

    
        </div>

      </div>
      
    </div>
</template>
<script>
    import { utilitiesMixin } from './../../mixins';
import IncompleteLoanRequestForm from './IncompleteLoanRequestForm.vue';

    export default {
        props: ["employments", "users", "application_fee", "monokey", "user", "loan_fee", "payurl", "affiliatecode", "loanreferenced", "topup", "request"],
        components : {
          'incompleteLoanRequestForm':
                IncompleteLoanRequestForm
        },
        mixins: [utilitiesMixin],
        
        data() {
            return {
                formData:{
                  comment: '',
                  code: ''
                },
                employment_id: '',
                amount: '',
                interest_percentage: '',
                duration: '',
                loanLimit: 0,
                success_fee: 0,
                charge: "",                                
                newAmount:"",
                setoff: "1",
                grossLoan: "",
                disbursal: "",
                loandocs: {},
                application_fee2: 0,
                statement: '',
                manageloan: '',
                referralMethod : 1,
                uploading : false,
                verifying : false,
                uploadPercentage : 0,
                section : '1',
                shouldCapitalize : true,
                enableMono : false,
                enableMbs : false,
                nextProcess : 1,
                upfront_status : 0,
                bankstatementcleared : false,
                old_data : {}                               
            };
        },

        mounted(){
          if (this.request) {
            this.old_data = JSON.parse(this.request.data);
          }
          //this.checkEnableMono();
        },
        
        methods: {

          monocall: function(){
              this.enableMono = 1;
            },

          mbscall: function(){
            this.enableMbs = 1;
          },
          

          async checkEnableMono(){
            await axios.get(`/bank-statement/check/retrieval/method/${this.user}`).then((res)=> {
              this.enableMono = res.data.status;
            });
          },
        
          payWithPaystack(){

              let name = this.user.name.split(',');

              let fname = name[1];

              let lname = name[0];

              let payfee = Math.round(this.application_fee2) * 100;

              const config = {
                    headers: { 'content-type': 'multipart/form-data' },
                    onUploadProgress: ( progressEvent ) => {
                        this.uploadPercentage =  Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total );
                    }
                }

              let $vm = this;

              var handler = PaystackPop.setup({
                  key: this.payurl,
                  email: this.user.email,
                  amount: payfee,
                  currency: 'NGN', 
                  firstname: fname,
                  lastname: lname,
            
                  callback: function(response) { 
                   
                    // Verify                    
                    $vm.createLoanRequest(response)
                  },

                  onClose: function() {
                  
                    swal("Transaction was not completed, window closed.");
                  },
            });
            handler.openIframe();
    
          },

          createLoanRequest(response){
              let vm = this;
              this.uploading = true;   

              var formData = new FormData();
              formData.append('reference', response.reference);
              formData.append('bank_statement', this.statement);
              formData.append('comment', this.formData.comment);
              formData.append('affiliate_code', this.formData.code == '' ? this.affiliatecode : this.formData.code);
              formData.append('employment_id', this.employment_id);
              formData.append('charge', this.charge);
              formData.append('duration', this.duration);
              formData.append('interest_percentage', this.interest_percentage);
              formData.append('amount', this.newAmount);
              //formData.append('manageloan', this.manageloan.manual_loan);
              formData.append('loan_referenced', this.loanreferenced);
              formData.append('is_top_up', this.topup);
              formData.append('bankstatementcleared', this.bankstatementcleared);
              formData.append('application_fee', this.application_fee2);
              
              formData.append('upfront_status', this.upfront_status);
              formData.append('enableMono', this.enableMono);
              axios.post(`/loan-requests/store`, formData).then(res => {
                  vm.uploading = false;
                  swal({
                    title: "Good job!",
                    text: "You loan request was successfully sent and awaiting approval, please check notification page for next step.",
                    icon: "success",
                    button: "OK",
                  }).then(res => {                  
                    window.location.reload();
                  });                    
                }).catch(err => {
                  vm.uploading = false;                
                  swal("Error occured!", `${err.response.data}`, "error");
              });
          },

          calculateMaxAmount() {
              let employment = this.employments.find((employment) => employment.id === this.employment_id);
              
              if (employment)
                  return employment.net_pay * this.duration / 3;
                  
              return "Loading...";
          },
          
          confirmAmount() {
              if (this.amount > this.max_amount) {
                  this.alertError(`You can't take more than your salary can handle for this duration ₦ ${this.max_amount.toLocaleString()}`);
                  this.amount = this.round(this.max_amount, 2) 
              }

              if (this.loanLimit != 0 && this.amount > this.loanLimit){
                this.alertError(`You can't take more than ₦ ${this.loanLimit.toLocaleString()}`);
                this.amount = this.round(this.loanLimit, 2)
              }
              
              let charge = this.newLoan(this.amount).charge;

                let employer = this.employment.employer;
                let loanamount = this.newLoan(this.amount).loanAmount;
                if(employer){
                    if(this.setoff != 1){
                      this.grossLoan = loanamount;
                      this.newAmount = loanamount;
                      this.disbursal = loanamount - charge;
                    }
                    if (this.setoff == 1) {
                      this.grossLoan = loanamount - charge;
                      this.newAmount = loanamount - charge;
                      this.disbursal = this.amount - charge;
                    }
                    this.charge = charge;  
                    this.upfront_status = 0; 
                }                    
            },

          newLoan(requestAmount) {
            let percentage = this.success_fee/100;
            let num = 1 - percentage;
            let loanAmount = requestAmount/num;
            let charge = loanAmount - requestAmount;
            return {loanAmount, charge};
          },

          useOldApplication(){


            // carry out functions
            let old_application_data = JSON.parse(this.request.data);
            console.log(old_application_data);
            this.amount = parseFloat(old_application_data.amount);
           
            this.employment_id = parseInt(old_application_data.employment_id);
           
            this.confirmAmount();

            //check and fix bankstatement retrieval
            if (old_application_data.bank_statement != '') {
              this.bankstatementcleared = true;
              this.statement = old_application_data.bank_statement;
            }
            this.old_request_used = true;
          },

          setOldApplicationDuration() {
              let old_application_data = JSON.parse(this.request.data);
              this.duration = parseInt(old_application_data.duration);
          },

          onChangeEmployer(){

            // // if there exists an old request that has not been used for the first time
            // if(!this.old_request_used && this.request != {}) {
            //     this.useOldApplication();
            // }

            if (this.employment_id != '') {
                let employment = this.employments.find((employment) => employment.id === this.employment_id);
                if (employment.employer.application_fee < 1){
                    this.application_fee2 = this.loan_fee + this.paystack_charge(this.loan_fee);
                }else{
                  this.application_fee2 = employment.employer.application_fee + this.paystack_charge(employment.employer.application_fee);
                }

                // add 100 if enabling mono
                this.application_fee2 += this.enableMono ? 100 : 0;
                
                this.loandocs = JSON.parse(employment.employer.loanRequestDocs);
                this.manageloan = JSON.parse(this.employment.employer.loanRequestSettings);
                this.shouldCapitalize = employment.employer.is_capitalized;
            }

            // We reste the duration to 0 so the user can enter and we validate
            this.duration = 0;

            // if there exists an old request that has not been used for the first time
            // if(!this.old_request_used && this.request != {}) {
            //     this.setOldApplicationDuration();
            // }
          },

          paystack_charge: function(amount){
            let charge = (1.5 / 100) * amount;

            if (amount > 2500) {

                charge += 100;
            }

            return this.round(charge, 2);
          },

          showStatementMethod(bankstatement){
            this.statement = bankstatement;
            this.bankstatementcleared = true;
          },
            
        },
        
        computed: {
            employment() {
              
                return this.employments.find((employment) => employment.id === this.employment_id);
            },
            
            emi() {
                
                let employment = this.employments.find((employment) => employment.id === this.employment_id);
                
                let feePercentage = 0;

                if (employment) {
                    if (this.duration <= 3) {
                        feePercentage = employment.employer.fees_3;
                        
                    } else if(this.duration > 3 && this.duration <= 6) {
                       
                        feePercentage = employment.employer.fees_6;
                    } else {
                        feePercentage = employment.employer.fees_12;
                    }
                }
                
                feePercentage = parseFloat(feePercentage);
               
                let rate = this.interest_percentage/100;
                let emi = this.pmt(this.newAmount,rate, this.duration);
                let fee = ((feePercentage/100) * this.newAmount);
               
                emi += fee;
                
                return typeof emi === 'number' && !isNaN(emi) && isFinite(emi) ? this.round(emi, 2) : 'Loading...';
            },

            total_repayment_fee() {
                
                let employment = this.employments.find((employment) => employment.id === this.employment_id);                
                let feePercentage = 0;
                if (employment) {
                    if (this.duration <= 3) {
                        feePercentage = employment.employer.fees_3;
                        
                    } else if(this.duration > 3 && this.duration <= 6) {
                       
                        feePercentage = employment.employer.fees_6;
                    } else {
                        feePercentage = employment.employer.fees_12;
                    }
                }                
                feePercentage = parseFloat(feePercentage);               
                let rate = this.interest_percentage/100;
                let emi = this.pmt(this.newAmount,rate, this.duration);
                let fee = ((feePercentage/100) * this.newAmount);               
                emi += fee;
                let total_repayment_fee = emi * this.duration;                
                return typeof total_repayment_fee === 'number' && !isNaN(total_repayment_fee) && isFinite(total_repayment_fee) ? this.round(total_repayment_fee, 2) : 'Loading...';
            },        
           
            
            max_amount() {
                let employment = this.employments.find((employment) => employment.id === this.employment_id);
                
                let max_amount = "Loading";
                
                if(employment) {
                  if(employment.employer.upgrade_enabled == 0){
                     this.loanLimit = employment.employer.loan_limit;
                     this.success_fee = employment.employer.success_fee;
                     max_amount = employment.net_pay * this.duration / 3;
                  }
                  if(employment.employer.upgrade_enabled == 1){
                    this.loanLimit = employment.employer.loan_limit;
                    this.success_fee = employment.employer.success_fee;                     
                    max_amount = (this.user.salary_percentage/100) * employment.net_pay * this.duration / 3;
                  }
                }
                return max_amount === "Loading" ? max_amount : this.round(max_amount, 2);
            },
            
            max_duration() {
                let employment = this.employments.find((employment) => employment.id === this.employment_id);
                
                let max_duration = "Loading";
                
                if (employment) {
                     return employment.employer.max_tenure;
                }
                return max_duration;
            },

           
        
        },
        
        watch: {
            employment_id: function(current) {
                let employment = this.employments.find((employment) => employment.id === current);
                
                if(employment) {
                    this.interest_percentage = employment.employer.rate_3 || 10;
                   // this.max_amount = this.calculateMaxAmount();
                }
            },

             referralMethod : function(val) {

                if (val == 2) {

                    $('.bootstrap-select').show();
                }else {

                    $('.bootstrap-select').hide();
                }
            },
            
            duration: function(current) {
                let employment = this.employments.find((employment) => employment.id === this.employment_id);
                if (current <= 3) {
                    this.interest_percentage = employment.employer.rate_3;
                } else if(current > 3 && current <= 6) {
                    this.interest_percentage = employment.employer.rate_6;
                } else {
                    this.interest_percentage = employment.employer.rate_12;
                }
                
                if (current > employment.employer.max_tenure) {
                    this.duration = employment.employer.max_tenure;
                }
            },

             
        },
        
    };
</script>