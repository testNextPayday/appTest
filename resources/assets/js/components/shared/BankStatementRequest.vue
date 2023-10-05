<template>
    <div class="" style="padding:5px;">
        <template v-if="!fetchingAccount">
            <form @submit.prevent="requestStatement" v-if="stage == 1">
            
                <div class="alert alert-danger" v-if="errMsg">{{errMsg}}</div>

                <div class="form-group">
                    <label class="form-control-label">Account Number</label>
                    <input type="text" class="form-control" disabled="true" :value="bankStatement.account_number">
                </div>

                <div class="form-group">
                    <label class="form-control-label">Bank </label>
                    <input type="text" class="form-control" disabled="true" :value="bankStatement.bank">
                </div>

                <div class="form-group">
                    <label class="form-control-label">Bank Phone Number</label>
                    <input type="text" class="form-control" disabled="true" :value="bankStatement.phone">
                </div>

                <span style="color:crimson"><b>NOTE :</b> Please ensure that the phone number entered below is the phone number attached to this account and you have up to NGN300 in your account </span><br><br>

                <button type="submit" class="btn btn-xs btn-primary"> <i :class="spinClass"></i> Capacity Check</button>

            </form>

            <form v-if="stage == 2" @submit.prevent="checkStatementRequest">
                <div class="alert alert-danger" v-if="errMsg">{{errMsg}}</div>
                <div class="alert alert-success" v-if="successMsg">{{successMsg}}</div>
                <button class="btn btn-danger" @click="stage--" v-if="errMsg"><i class="fa fa-chevron-left"></i> Back</button>
                <div class="alert alert-success" v-if="!errMsg"> ! Success Waiting to check the status of your request <br> <i class="fa fa-clock-o"></i> {{currentTime}} </div>
                <div class="" v-if="checking"> <i :class="spinClass"></i> Checking Request Status </div>
            </form>

            <form v-if="stage == 3" @submit.prevent="confirmStatementRequest" class="form">
                <div class="alert alert-info">Please enter the ticket no and password sent to your phone. This might take some time please wait </div>

                <div class="alert alert-danger" v-if="errMsg">{{errMsg}}</div>
                
                <div class="form-group">
                    <label class="form-control-label" for="ticket_no">Enter Ticket No.</label>
                    <input class="form-control" name="ticket_no" v-model="ticketNumber" required>
                </div> 

                <div class="form-group">
                    <label class="form-control-label" for="ticket_password">Enter Ticket Password</label>
                    <input class="form-control" type="password" name="ticket_password" v-model="ticketPassword" required>
                </div>   

                <div class="form-group">
                    <button type="submit" class="btn btn-xs btn-primary"> <i :class="spinClass"></i> Confirm Statement Request</button>
                    <a href="#" class="btn btn-xs danger" @click="this.stage = 1"> <i :class="spinClass"></i> Start Again</a>
                </div> 
            </form>

            <form v-if="stage == 4" @submit.prevent="checkStatementRequestByTicket">
                <div class="alert alert-danger" v-if="errMsg">{{errMsg}}</div>
                <div class="alert alert-success" v-if="successMsg">{{successMsg}}</div>
                <!-- <button class="btn btn-danger" @click="stage--" v-if="errMsg"><i class="fa fa-chevron-left"></i> Back</button> -->
                <div class="alert alert-success" v-if="!errMsg"> ! Hold on while we verify the status of the ticket that was just sent <br> <i class="fa fa-clock-o"></i> {{currentTime}} </div>
                <div class="" v-if="checking"> <i :class="spinClass"></i> Checking Request Status </div>
            </form>


            <form v-if="stage == 5" @submit.prevent="retrieveBankStatement">
                <div class="alert alert-danger" v-if="errMsg">{{errMsg}}</div>
                <div class="alert alert-success" v-if="retrievalStatus == false"> ! Hold on while we attempt to retrieve bank Statement <br> <i class="fa fa-clock-o"></i> {{currentTime}} </div>
                <div class="alert alert-success" v-if="retrievalStatus"> <a :href="pdfDocLink" target="_blank"><i class="fa fa-file-pdf-o"></i></a>  Statement has been retrieved proceed to book loan  </div>
                <div class="" v-if="checking"> <i :class="spinClass"></i> Retrieving Statement</div>
                
            </form>
        </template>

        <template v-else>
            <h5> <i class="fa fa-spinner fa-spin"></i> Getting account details ..</h5>
        </template>

    </div>
</template>

<script>
import {utilitiesMixin} from './../../mixins';

    export default {
        name : 'my-bank-statement',
        mixins : [utilitiesMixin],
        props : {
            user : {
                type: String
            }
        },
        data() {
            return {
                stage : 1,
                requestID : '',
                errMsg : '',
                checking : false,
                ticketNumber : '',
                ticketPassword : '',
                timer : 0,
                interval : '',
                message : '',
                retrievalStatus : false,
                bankStatement : {},
                pdfDocLink : '#',
                retrievalTries : 0,
                maxTries : 3,

                maxRetrievalTries: 6,
                successMsg: '',
                requestTries : 0,
                ticketTries : 0,
                fetchingAccount : false


            };
        },

        mounted() {

            this.getAccountDetails();
            console.log('Mounting component again')
        },

        computed: {

            currentTime() {

                let minutes = Math.floor(this.timer / 60);
                let seconds  = this.timer - minutes * 60;

                return minutes+ ' : '+ seconds;
            },

            userReference() {
                return this.user;
            }
        },
        watch : {

            timer(val) {

                if (val == 0) {

                   clearInterval(this.interval);

                   this.carryOutNextTask();
                   
                }
            },

            userReference(currentvalue) {

                this.getAccountDetails();
            }
        },

        methods : {

            startCountDown(minutes){

                this.timer = minutes * 60; // converted to seconds

                this.interval = setInterval(()=> { this.timer -= 1; }, 1000);
            },

            refreshData(){
                Object.assign(this.$data, this.$options.data());
            },

            carryOutNextTask(){

                switch(this.message) {

                    case 'checking-request-id' : 
                        this.checkRequestStatus();
                    break;

                    case 'checking-request-ticket':
                            this.checkStatementRequestByTicket();
                    break;

                    case 'reconfirming-request':
                        this.reConfirmStatement();
                    break;

                    case 'retrieving-statement': 
                        this.retrieveBankStatement();
                    break;
                }
            },

            async requestStatement() {

                this.startLoading();

                 this.clearErrors();

                 const req = {reference : this.user};

                await axios.post(`/bank-statement/request`, req).then((res)=> {

                   
                    if (res.data.status == '007') {

                        this.stage = 5;

                        this.retrievalStatus = true;

                        this.pdfDocLink = res.data.result;

                         this.$emit('cleared', this.pdfDocLink);

                    }else {
                        this.requestID = res.data.requestID;

                        this.stage = 2; // Waiting for a minute to request status of request sent

                        this.message = 'checking-request-id';

                    this.startCountDown(0.1);
                    }
                    

                }).catch((e)=> {
                    
                    this.handleErrorResponseFromAPI(e);
                });

                this.stopLoading();
            },

            async checkRequestStatus() {

                this.startLoading();

                 this.clearErrors();

                this.checking = true;

                this.requestTries++;

                await axios.post(`/bank-statement/checkRequest/${this.requestID}`).then((res)=> {
                    this.successMsg = res.data.result.feedback;
                    let result = res.data.result;

                    if (res.data.status == "00" && result.status != 'Pending') {

                       let successStatus = ['Success', 'Sent', 'Ticket', 'TicketSent'];
                       if (result && successStatus.includes(result.status) == false) {
                           throw result.feedback;
                       }
                        this.stage = 3; // moving to the confirmation stage
                        
                    }else {

                           this.requestAttempts();                
                   }
                   
                }).catch((e)=> {

                    let response = e.response;
                    
                    response ? this.handleErrorResponseFromAPI(e) : this.handleErrorFromCode(e);

                    this.stage = 1;

                });

                this.checking = false;

                this.stopLoading();
            },

            requestAttempts() {
                 if (this.requestTries <= this.maxTries) {
                    this.message = 'checking-request-id';
                    this.startCountDown(0.1);

                } else {

                    this.stage = 1;
                } 
            },

            async confirmStatementRequest() {

                this.startLoading();

                 this.clearErrors();

                const request = {ticketNo : this.ticketNumber, password : this.ticketPassword, requestID : this.requestID};

                await axios.post(`/bank-statement/confirm`, request).then((res)=> {

                    //if (res.data.status == "00") {

                       this.stage = 4; // where we check ticket status 

                       this.message = 'checking-request-ticket';
                       this.startCountDown(0.3);

                    //}


                }).catch((e)=> {
                    this.handleErrorResponseFromAPI(e);
                });

                this.stopLoading();
            },

            async checkStatementRequestByTicket() {

                this.startLoading();

                 this.clearErrors();

                this.checking = true;

                this.ticketTries++;

                await axios.post(`/bank-statement/checkRequest/ticket/${this.ticketNumber}`).then((res)=> {
                   
                   if (res.data.status == "00") {

                       let result = res.data.result; 

                       if (result && (result.status == "Error" || result.status == "Insfund")) {
                           throw result.feedback;
                       }

                       this.stage = 5; // moving to the statement retrieval stage

                       this.message  = 'retrieving-statement';

                       this.startCountDown(0.1);

                       // TODO ERROR HANDLING
                   }else {

                        if (this.ticketTries <= this.maxTries) {
                            this.stage = 5; // moving to the statement retrieval stage

                            this.message  = 'checking-request-ticket';

                            this.startCountDown(0.1);

                        }else {

                            // go back to stage one
                            this.stage = 1;
                        }
                   }
                }).catch((e)=> {

                    let response = e.response;
                    
                    response ? this.handleErrorResponseFromAPI(e) : this.handleErrorFromCode(e);
                    
                    this.stage = 1;
                
                });

                this.checking = false;

                this.stopLoading();
            },

            handleErrorFromCode(e) {

                 this.errMsg = e;
            },

             async reConfirmStatement() {

                this.startLoading();

                 this.clearErrors();

                await axios.post(`/bank-statement/reconfirm/${this.requestID}`).then((res)=> {

                    if (res.data.status == "00") {

                        this.stage = 4; // where we check ticket status 

                       this.message = 'checking-request-ticket';
                       this.startCountDown(0.1);

                   }


                }).catch((e)=> {

                    this.handleErrorResponseFromAPI(e); 
                });

                this.stopLoading();
            },

            async retrieveBankStatement() {

                this.startLoading();

                this.clearErrors();

                this.retrievalTries++;

                await axios.get(`/bank-statement/retrieve-statement/${this.ticketNumber}`).then((res)=> {
                   

                    if (res.data.status == "00") {

                        this.retrievalStatus = true;

                        this.pdfDocLink = res.data.result;

                        this.$emit('cleared', this.pdfDocLink);

                       // PROCEED TO MAKE LOAN REQUEST

                    }


                }).catch((e)=> {
                    this.handleErrorResponseFromAPI(e);
                    
                    if (this.retrievalTries <= this.maxRetrievalTries) {
                        this.stage = 5; // moving to the statement retrieval stage

                        this.message  = 'retrieving-statement';

                        this.startCountDown(0.1);

                    }else {

                        // go back to stage one
                        this.stage = 1;
                    }
                     
                });

                this.stopLoading();
            },

            async getAccountDetails() {

                this.fetchingAccount = true;

                await axios.get('/bank-statement/user-details', {params: { reference : this.user}}).then((res)=> {
                    if (res.data.can_request == false) {
                        this.$emit('showstatement');
                    }
                    this.bankStatement = res.data;
                })
                this.fetchingAccount = false;
                // Ensure the stage is 1
                this.stage = 1;
            },

            clearErrors() {

                this.errMsg = "";
                this.successMsg = '';
            },

            handleErrorResponseFromAPI(e) {
                //console.log(e)
                this.errMsg = e.response.data
            }

        }



    }
</script>