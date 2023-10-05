<template>
    
    <div>

        <div v-if="methods.filter((item)=> item.status < 2).length > 0">

            <div class="collection_methods" v-for="(method,index) in methods" :key="index">

                <h4>Setup Method {{index + 1}}</h4>

                <hr>
                

                <div v-if="method.status == 2" class="text-center setup-done">
                    <p>A Global Standing Instruction(GSI) has been setup on this loan.</p>
                    <h4><i class="fa fa-3x fa-check-square" style="color:#4ec04e"></i></h4>
                    <p>If this loan is unpaid on due date, I authourize Nextpayday to activate GSI through our Partner Bank (WEMA Bank),
                         on all accounts held by me in Deposit banks and platforms.</p>

                </div>

                <div v-else>

                    <div class="method row" v-if="method.code === '300'">

                        <div class="col-md-6 offset-md-2">

                            <p>Your loan has been setup to use card . Please click the link below to add your card </p>

                            <button class="btn btn-block btn-success btn-rounded" @click="goToGateway">
                            <i :class="spinClass"></i>
                            {{payButtonText}}
                            </button>
                    
                            <!-- This loan is using paystack -->
                            <paystack :pay-key="paykey" ref="paystack" :email="user.email" :amount="verificationFee" @paystack-response="paystackResponse" :reference="reference"></paystack>
                        
                        </div>
                        

                    </div>

                    <div class="method row" v-if="method.code === '101'">

                        <div class="col-md-6 offset-md-2">

                            <p>Your loan has been setup to use Okra . Please click the link to setup your account </p>

                            <button class="btn btn-block btn-success btn-rounded" @click="connectViaOptions">
                            <i :class="spinClass"></i>
                            {{okraSetupButton}}
                            </button>
                           
                        </div>                        

                    </div>

                    <div class="method row" v-if="method.code === '102'">

                        <div class="col-md-6 offset-md-2">

                            <p>Your loan has been setup to use Mono . Please click the link to setup your account </p>

                            <button class="btn btn-block btn-success btn-rounded" @click="monoPaymentWidget">
                            <i :class="spinClass"></i>
                            {{monoSetupButton}}
                            </button>
                           
                        </div>                        

                    </div>

                    <div class="method" v-if="['100','200'].includes(method.code)">

                        <div class="col-md-6 offset-md-2">

                            <p>Your loan has been setup to use Remita </p>

                            <!-- This loan is using DDM Remita(100) or DAS Remitta (200) -->
                            <remita-otp-form :loan="mutableLoan" :bank="banks[0]" @refresh="regenerateLoan" :mandateurl="mandateurl" :otpenabled="otpenabled"></remita-otp-form>
                        
                        </div>

                        
                    </div>

                    
                    <div class="method" v-if="['201','202','203'].includes(method.code)">
                        <!-- This loan is using IPPIS(201) or RVSG(202) upload the authority form -->
                        <authority-form-upload :loan="mutableLoan"></authority-form-upload>
                    </div>

                </div>

            

            </div>

        </div>


        <div v-else>


            <div class="text-center">

                <h3 style="margin-bottom : 20px;"><span class="text-success">Success</span> Loan Setup was Successful</h3>

                <div>

                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 342 342" width="342" height="342" preserveAspectRatio="xMidYMid meet" style="width: 25%; height: 100%; transform: translate3d(0px, 0px, 0px);"><defs><clipPath id="__lottie_element_2"><rect width="342" height="342" x="0" y="0"></rect></clipPath></defs><g clip-path="url(#__lottie_element_2)"><g class="circle" transform="matrix(3,0,0,3,0,0)" opacity="1" style="display: block;"><g opacity="1" transform="matrix(1,0,0,1,57,57)"><path stroke-linecap="round" stroke-linejoin="round" fill-opacity="0" stroke="rgb(133,203,130)" stroke-opacity="1" stroke-width="2" d=" M47.49599838256836,-29.68199920654297 C52.8849983215332,-21.07699966430664 56,-10.902000427246094 56,0 C56,30.92799949645996 30.92799949645996,56 0,56 C-30.92799949645996,56 -56,30.92799949645996 -56,0 C-56,-30.92799949645996 -30.92799949645996,-56 0,-56 C0,-56 0,-56 0,-56 C20.025999069213867,-56 37.59700012207031,-45.487998962402344 47.49599838256836,-29.68199920654297"></path></g></g><g class="checkmark" transform="matrix(3,0,0,3,0,0)" opacity="1" style="display: block;"><g opacity="1" transform="matrix(1,0,0,1,58.5,60.5)"><path stroke-linecap="butt" stroke-linejoin="miter" fill-opacity="0" stroke-miterlimit="4" id="colorCheckmark" stroke="rgb(133,203,130)" stroke-opacity="1" stroke-width="2" d=" M-32.5,-0.41100001335144043 C-32.5,-0.41100001335144043 -10.559000015258789,21.5 -10.559000015258789,21.5 C-10.559000015258789,21.5 32.5,-21.5 32.5,-21.5"></path></g></g></g></svg>

                </div>

               
            </div>

        </div>

        


      
    </div>

</template>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.okra.ng/v2/bundle.js"></script>
<script>

    import Paystack from './../Paystack';
    
   
    import { utilitiesMixin } from './../../mixins';


    export default {

        mixins: [utilitiesMixin],
        
        

        components : {

            'paystack':Paystack            
        },

        props : {

            loan : {
                type : Object,
                required: true
            },

            debitstartdate: {
                type: String,
                required: true
            } ,

            debitenddate:{
                type: String,
                required:true
            },

            paykey : {
                type : String,
                required: true
            } ,

            user : {
                type :Object,
                required: true
            },

            banks : {
                type :Array,
                required : true
            },
            mandateurl : {
                type : String,
                required : true
            },
            otpenabled : {
                type : Boolean,
                required : true
            }

        },
      
        data(){

            return{

                verificationFee : 100,

                okraSetupFee : 21000,

                payButtonText : 'Add Card Setup',

                mutableLoan : this.loan,

                okraSetupButton: 'Setup Collection',

                monoSetupButton: 'Setup Collection',

                imgPath:'../../logo_pack/logo/colored/1024.png',

                reference : '',

                monoPaymentLink : this.loan.mono_payment_link
                
            };
        },    


        computed : {
            methods(){
                return JSON.parse(this.mutableLoan.collection_methods)
            }
        },

        mounted(){
            this.getTransReference();

            let jqueryScript = document.createElement('script');
            jqueryScript.setAttribute('type','text/javascript');               
            jqueryScript.setAttribute('src', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
            document.head.appendChild(jqueryScript);

            let okraScript = document.createElement('script');
            okraScript.setAttribute('type','text/javascript');   
            okraScript.setAttribute('src', 'https://cdn.okra.ng/v2/bundle.js');               
            document.head.appendChild(okraScript);
             
        },

        methods : {

            paystackResponse(event) {

                console.log(event);

                this.startLoading();

                this.payButtonText = "Verifying Payment";
                
                const request = {reference : event.reference , amount : this.verificationFee}

                axios.post(`/loan-setup/card-setup/verify/${this.loan.reference}`, request).then(response => {

                    if(response.data.status === true) {
                        this.alertSuccess(response.data.message);
                        this.regenerateLoan();
                    } else {
                        this.alertError(response.data.message);
                    }

                }).catch(error => {
                    this.alertError('An error occurred. Please try again');
                });
                  this.stopLoading();
            },
            async goToGateway() {
                this.startLoading();

                await this.$refs.paystack[0].initiatePayment();

                this.stopLoading();

            },

            monoPaymentWidget(){
                window.open(this.monoPaymentLink);
            },
            async regenerateLoan(){

                await axios.get(`/loan/${this.mutableLoan.reference}`).then((res)=>{

                    this.mutableLoan = res.data;

                }).catch((e)=>{

                    this.alertError(e.response.data);
                })
            },
            async getTransReference(){
                await axios.get('/paystack/reference').then((res)=> {
                    this.reference = res.data;
                })
            },  

            connectViaOptions(){    
                const dis = this;        
                Okra.buildWithOptions({
                name: 'Nextpayday ',
                env: 'production',
                key: '5f4d65c2-9261-56df-9e26-30da82302f59',
                token: '60c1f0e143df4a3004d96b7c', 
                source: 'integration',
                color:  '#3a43b7',
                limit: '6',        
                corporate: null,
                connectMessage: 'Select an account to continue',        
                products: ["auth","identity","balance","transactions","income"],
                debitLater: true,
                //debitAmount: 10000, // optional kobo amount 
                debitType: 'ongoing',
                callback_url: 'https://webhook.site/0949f586-45a4-4dfc-815f-8df26a514a89',
                redirect_url: '',
                logo: '',
                institutions: ["first-bank-of-nigeria","united-bank-for-africa","guaranty-trust-bank","access-bank","zenith-bank","kuda-bank","stanbic-ibtc-bank","first-city-monument-bank"],
                widget_success: 'Okra Authentication Completed. Click Exit to View Connection Status ',
                widget_failed: 'An unknown error occurred, please try again.',
                currency: 'NGN', 
                mode: 'light', 
                continue_cta: 'Exit', 
                multi_account: 'false',
                exp: null,            
                 
                options:{
                    loanId: this.loan.id,                    
                },        
                success_title: 've successfully linked to nextpayday!',
                success_message: 'With your bank account linked, you would be able to access the best financial services & products.',
                auth: {"manual":false,"debitLater":true,"debitType":"ongoing"},
                balance: {"showBalance":true,"enableAutoConnect":true}, 
                
                onSuccess: function(data){                           
                       dis.startLoading();
                       dis.okraSetupButton = "Please Wait, Verifying Connection";
                        let bankId = data.bank_id;
                        let customerId = data.customer_id;
                        let recordId = data.record_id;
                        let accountId = data.accounts[0].id;
                        let accountNumber = data.accounts[0].nuban;
                        let loanId = data.options.loanId;
                        
                        let pauseParams = {
                            bankId : bankId,   
                            customerId:customerId,
                            recordId:recordId,
                            accountId:accountId,
                            accountNumber:accountNumber,
                            loanId:loanId                  
                        }

                        axios.post('/users/okra/webhook', pauseParams).then((response)=>{ 
                            if(response.data.status === true) {
                                dis.alertSuccess(response.data.message);                               
                                                               
                            } else {
                                dis.alertError(response.data.message);
                                dis.okraSetupButton = "Setup Collection";
                            }
                        }).catch((error)=>{
                            dis.alertError(error);
                            dis.okraSetupButton = "Setup Collection";
                        });
                    dis.stopLoading();
                    dis.okraSetupButton = "Setup Collection";
                },
                onClose: function(){
                    console.log('closed')
                }
            });
        }  
    }
}
</script>

<style>
    .collection_methods h4 {
        font-size:28px;
    }
    .setup-done {
        padding: 30px;
    }
</style>