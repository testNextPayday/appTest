<template>
  <div>
    <div class="card">
      <div class="card-body">

     <div class="d-flex align-items-center justify-content-between">
          <h4>Book a new Loan</h4>
          <h5 class="pull-right">Processing Fee &nbsp;<span class="badge badge-primary" v-if="application_fee != undefined">{{application_fee2 < 1 ? application_fee : application_fee2}}</span></h5>  
    </div>
    <br/> 

    <div class="form-group" v-if="!user">
      <label for="reference">User Account</label>
      <input type="text" name="user_query" v-model="query" class="form-control" placeholder="Search Borrower Name">
      <select class="form-control" name="reference" v-model="reference" @blur="onChangeEmployer($event)" placeholder="Please Select Name" @onchange="onChangeEmployer($event)"  onblur="formButton()">
        <template v-if="users.length > 0"><option v-for="user in users" :key="user.id" :value="user.reference">{{user.name}}</option></template>

        <template v-else>
          <option selected="selected">No User found</option>
        </template>
      </select>
     
    </div>

    <div class="form-group" v-else>
      <label for="reference">User Account</label>
      <select class="form-control selectpicker" name="reference" v-model="reference" @blur="onChangeEmployer($event)" @onchange="onChangeEmployer($event)" data-live-search="true">
        <option :value="user.reference" selected>{{user.name}}</option>
      </select>
    </div>

    <div class="form-group">
      <label for="reference">User Employment</label>
      <select class="form-control" name="employment" v-model="employment" @onchange="onChangeEmployer($event)"  @blur="onChangeEmployer($event)" required>
        <option v-if="selectedUser.employments.length" v-for="employment in selectedUser.employments" :value="employment.id">{{employment.employer.name}} </option>
        <!-- - ₦ {{employment.net_pay}} -->
      </select>
      <span v-if="noPayRollID" style="color:red"><h6>The employment selected for this customer has no Payroll ID. Please update before applying for the loan. </h6></span>
    </div>

    <template v-if="loandocs.bank_statement == true && allowStatementUpload == false">
          <my-bank-statement @cleared="bankstatementcleared = true" @showstatement="showStatementMethod" :user="reference" ref="bankstatementrequest"></my-bank-statement>
      </template>

     <template v-if="(loandocs.bank_statement == true && bankstatementcleared == true) || (loandocs.bank_statement == false)">

      <div class="form-group">
        <label for="duration">Loan Duration (Months)</label>
        <input
          type="number"
          class="form-control"
          name="duration"
          v-model="duration"
          placeholder="How long will it take to payback loans"
          v-on:keyup="checkMaxRequestAmount"
          @change="validateDuration"
          required
        />
      </div>


        <div class="form-group">
          <label for="company">Amount Needed</label>
          <input
            type="number"
            class="form-control"
            id="amount"
            v-model="amount"
            name="amount"
            placeholder="Enter an amount in Naira"
            v-on:keyup="max_amount_needed"
            @blur="max_amount_needed"
            required
          />
        </div>

      <input type="hidden" name="newAmount" v-model="newAmount">

      <input type="hidden" name="bankstatementcleared" v-model="bankstatementcleared">

      <input type="hidden" name="charge" v-model="charge">

        <div class="">
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

      <div class="row mb-2">
        <div class="col">
          <div class="float-right font-weight-bold">
            <label class="active">
              <input type="radio" value="1" v-model="setoff" required @change="max_amount_needed"> Set-off
            </label>

            <label class="">
              <input type="radio" value="0" v-model="setoff" required @change="max_amount_needed"> Capitalize 
            </label>
          </div>
        </div>
      </div>

      <h5 class="mt-2 mb-4">
        <button
          type="button"
          class="btn btn-xs btn-primary"
          @click="checkMonthlyRepayment"
          :disabled="loading">
          <i :class="spinClass"></i>
          {{emiButtonText}}
        </button>
        <span class="pull-right">
          <strong>{{emi}}</strong>
        </span>
      </h5>

        <template v-if="loandocs.bank_statement == true & allowStatementUpload == true">
        <div class="form-group">
          <label for="bank_statement" style="display:block;">Bank Statement/Pay Slip (3 months from today, JPG or PDF)
            <span class="pull-right">Max Size: 1MB</span>
          </label>
          <input type="file" class="form-control" name="bank_statement" id="bank_statement" required>
        </div>
      </template>        
    
        <div class="row">
          <div class="form-group col-sm-12">
            <label for="textarea-input">Purpose of Loan</label>
            <textarea name="comment" id="comment" rows="3" class="form-control" placeholder="This is your opportunity to increase your chances of getting a Loan"></textarea>
          </div>  
        </div>
        <br/>









        <div class="form-group">
          <label for="reference">Assign Affiliate</label>
          <select name="affiliate_id" id="" required class="form-control form-select">
                            <option disabled> Select affiliate</option>
                            <option v-for="affiliate in affiliates" :value="affiliate.id">{{ affiliate.name }}</option>
                        </select>
         
        </div>













        <div class="checkbox">
          <label for="checkbox2">
            <input type="checkbox" id="checkbox2" name="accept_insurance" required> 
            I agree to the <a href="http://nextpayday.ng/terms" target="_blank">terms and conditions</a> of Nextpayday
          </label>
        </div>
        <br/>

      <button type="submit" class="btn btn-sm btn-primary" id="booking-form"><i class="fa fa-dot-circle-o"></i> Submit</button>

    </template>   

    
      </div>
    </div>
  </div>
</template>
<script>
import { utilitiesMixin } from "../../mixins";
import {mapGetters} from 'vuex';
export default {
  props: ["url", "emiUrl", "users-url", "user", "application_fee"],

  mixins: [utilitiesMixin],

  data() {
    return {
      reference: "",
      duration: "",
      max_amount: "",
      buttonText: "Check MAX Amount",
      emiButtonText: "Check EMI",
      selectedUser: this.user ? this.user : { employments: [] },
      amount: "",
      withdrawDate: "",
      employment: {},
      emi: "",
      noPayRollID : false,
      loanLimit: 0,
      success_fee: 0,
      newAmount:"",
      setoff: "1",
      charge: "",
      grossLoan: "",
      disbursal: "",
      loandocs: {},
      application_fee2: 0,
      bankstatementcleared : false,
      allowStatementUpload : false,
      key : 1,
      query : '',
      affiliates: null,
      affiliate: null




    };
  },

  async mounted() {
    
    if (this.user) {
  
     
      this.reference = this.user.reference;
      this.selectedUser = this.user;
      if (this.user.employments.length)
        this.employment = this.user.employments[0].id;
    }

    this.getAffiliates();
    
  },
  methods: {

    async getAffiliates(){
      await axios.get('/staff/get-all-affiliates').then((response) => {
          this.affiliates = response.data.data
          console.log(this.affiliates)
      }

      );
    },


     showStatementMethod(){
        this.allowStatementUpload = true;

        this.bankstatementcleared = true;
      },

    validateDuration() {

      if (this.selectedEmployment) {
        let employer = this.selectedEmployment.employer
        if (this.duration > employer.max_tenure) {
          this.duration = employer.max_tenure;
          this.alertError('You cannot take more than '+ employer.max_tenure+ ' months');
        }
      }
     
    },
    async checkMaxRequestAmount() {
      if (!this.reference) return this.alertError("Please provide a reference number");
      if (!this.duration) return this.alertError("Please provide a duration");
      if (!this.employment) return this.alertError("No employment selected");

      try {
        this.startLoading();
        this.buttonText = "Checking ...";

        const response = await axios.get(
          `${this.url}/${this.reference}/${this.duration}/${this.employment}`
        );

        this.max_amount  = response.data.max_amount;
        this.loanLimit   = response.data.loanLimit;
        this.success_fee = response.data.success_fee;
        // console.log("hi"+response.data.loanLimit);
        // this.alertSuccess("Success!");

        this.stopLoading();
        this.buttonText = "Check MAX Amount";
      } catch (e) {
        this.buttonText = "Check MAX Amount";
        this.handleApiErrors(e);
        this.stopLoading();
      }
    },

    async checkMonthlyRepayment() {
      if (!this.amount) return this.alertError("Please provide an amount");
      if (!this.duration) return this.alertError("Please provide a duration");
      if (!this.employment) return this.alertError("No employment selected");

      try {
        this.startLoading();

        this.emiButtonText = "Checking ...";

        const response = await axios.get(
          `${this.emiUrl}/${this.duration}/${this.employment}/${this.newAmount}`
        );

        this.emi = `₦ ${response.data.emi}`;

        this.emiButtonText = "Check EMI";
        this.alertSuccess("Success!");
        this.stopLoading();
      } catch (e) {
        this.emiButtonText = "Check EMI";
        this.handleApiErrors(e);
        this.stopLoading();
      }
    },

    max_amount_needed(){

       if (this.loanLimit != 0 && this.amount > this.loanLimit) {
             this.alertError(`You can't take more than ₦ ${this.loanLimit.toLocaleString()}`);
             this.amount = this.round(this.loanLimit, 2);
        }

      if (this.loanLimit == 0 && this.amount > this.max_amount) {
           this.alertError(`You can't take more than ₦ ${this.max_amount.toLocaleString()}`);
           this.amount = this.round(this.max_amount, 2);
       }
        
       
         let charge = this.newLoan(this.amount).charge;
         let loanamount = this.newLoan(this.amount).loanAmount;

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

    },

    newLoan(requestAmount) {
      let percentage = this.success_fee/100;
      let num = 1 - percentage;
      let loanAmount = requestAmount/num;
      let charge = loanAmount - requestAmount;
      return {loanAmount, charge};
    },

    onChangeEmployer(event){
      if (this.employment != '' && this.employment != 0) {
          let employment = this.selectedEmployment;
          this.application_fee2 = employment.employer.application_fee + this.paystack_charge(employment.employer.application_fee);
          this.loandocs = JSON.parse(employment.employer.loanRequestDocs);
          this.duration = 0; // setting the duration to 0
           this.allowStatementUpload = false;
           this.bankstatementcleared = false;
           console.log('working from home')
      }
    },

    reRenderBankStatement(){

      // Writing this function try and re render this component .. No blame me
      this.$refs.bankstatementrequest.$mount();
    },

    paystack_charge: function(amount){
        let charge = (1.5 / 100) * amount;

        if (amount > 2500) {

            charge += 100;
        }

      return this.round(charge, 2);
    },

    searchBorrowers() {
      
      let userQuery = this.query;
      let url = this.usersUrl;
      this.$store.dispatch('searchLoanBorrowers', {
        url,
        userQuery
      })

    }

  },

  computed: {
    selectedEmployment() {
      return this.selectedUser.employments.find(
        employment => employment.id === this.employment
      );
    },
    users() {
      return this.loanRequestBorrowers;
    },
    ...mapGetters(['loanRequestBorrowers'])
  },

  watch: {
    selectedEmployment : function(emp){
      if(emp && emp.payroll_id == null){
        this.noPayRollID = true;
        // this.disableLoanBooking();
      }else{
        this.noPayRollID = false;
        // this.enableLoanBooking();
      }
    },
    reference: function(current) {

      // try to get from all the users
      // if no user exists check if there is a this.user and set to user 
      let user;
      if(user = this.users.find(user => user.reference === current)) {

      }else {
        if (this.user) {
          user = this.user;
        }
      }
      
  
      if (user) {
        this.selectedUser = user;
        if (user.employments.length) this.employment = user.employments[0].id;
        else this.employment = 0;
      } else this.selectedUser = { employments: [] };
      this.onChangeEmployer(event);
    },

    employment: function(newvalue, oldvalue) {
        this.onChangeEmployer(event);
    },

    query : {
          handler: _.debounce(function(){
              this.searchBorrowers();
          },0)
    }
   
  },
};
</script>