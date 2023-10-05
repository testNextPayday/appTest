<template>
    <div class="card mx-4">
        <div class="card-body p-4">
            <div class="col-xs-12 col-sm-12 text-center">
                <img class="img-responsive" style="width:15em;" src="/logo_pack/logo/colored/128.png"/>
            </div>
            <br/>
            <h3 class="text-muted strong text-center">REGISTER</h3>
            <p class="text-muted">Create your account 
                <span class="pull-right" style="cursor: pointer;" @click="step = 1" v-if="step !== 1">
                    <i class="fa fa-refresh"></i> Start Over
                </span>
            </p>
            
            <h5 class="text-danger text-center">
                 {{errorMessage}}   
            </h5>
            <ul v-if="errorBag.length > 0">
                <li v-for="error in errorBag" class="text-danger">{{error}}</li>
            </ul>
            
            
            <div class="row" v-if="step === 1">
                <div class="col-xs-12 col-sm-6">
                    <a href="#" @click="logBorrowerLender(1)" class="btn btn-block btn-primary">
                        <strong>
                            <i class="icon-arrow-down-circle"></i>&nbsp;
                            Register as a borrower
                        </strong>
                    </a>
                    <br/>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <a href="#" @click="logBorrowerLender(2)" class="btn btn-block btn-success">
                        <strong>
                            <i class="icon-arrow-up-circle"></i>&nbsp;
                            Register as an Investor
                        </strong>
                    </a>
                    <br/>
                </div>
            </div>
            
            <div class="row" v-if="step === 2">
                <div class="col-xs-12 col-sm-6">
                    <a href="#" @click="logIndividualCompany(1)" class="btn btn-block btn-info">
                        <strong>
                            <i class="icon-user"></i>&nbsp;
                            Individual
                        </strong>
                    </a>
                    <br/>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <a href="#" @click="logIndividualCompany(2)" class="btn btn-block btn-warning">
                        <strong>
                            <i class="icon-briefcase"></i>&nbsp;
                            Company
                        </strong>
                    </a>
                    <br/>
                </div>
            </div>
            
            <form class="form-horizontal" @submit.prevent="register" method="POST" v-if="step === 3">
                <div v-if="registrationData.individual">
                    <div class="input-group mb-3">
                        <span class="input-group-addon"><i class="icon-user"></i></span>
                        <input type="text" name="firstname" v-model="registrationData.firstname" class="form-control" required placeholder="Firstname">
                    </div>
                
                    <div class="input-group mb-3">
                        <span class="input-group-addon"><i class="icon-user"></i></span>
                        <input type="text" name="lastname" v-model="registrationData.lastname" class="form-control" required placeholder="Lastname">
                    </div>
                
                    <div class="input-group mb-3">
                        <span class="input-group-addon"><i class="icon-user"></i></span>
                        <input type="text" name="midname" v-model="registrationData.midname" class="form-control" placeholder="Middlename">
                    </div>
                </div>
                <div v-else>
                    <div class="input-group mb-3">
                        <span class="input-group-addon"><i class="icon-user"></i></span>
                        <input type="text" name="company_name" class="form-control" v-model="registrationData.company_name" required placeholder="Company Name">
                    </div>
                </div>
                
                <div class="input-group mb-3">
                    <span class="input-group-addon">@</span>
                    <input type="email" name="email" v-model="registrationData.email" class="form-control" required placeholder="Email">
                </div>
                
                <div class="input-group mb-3">
                    <span class="input-group-addon"><i class="icon-phone"></i></span>
                    <input type="number" name="phone" v-model="registrationData.phone" class="form-control" required placeholder="Phone">
                </div>
    
                <div class="input-group mb-3">
                    <span class="input-group-addon"><i class="icon-lock"></i></span>
                    <input type="password" name="password" v-model="registrationData.password" class="form-control" required placeholder="Password">
                </div>
    
                <div class="input-group mb-4">
                    <span class="input-group-addon"><i class="icon-lock"></i></span>
                    <input type="password" name="password_confirmation" v-model="registrationData.password_confirmation" class="form-control" required placeholder="Repeat password">
                </div>
                
                <div class="input-group mb-4">
                    <span class="input-group-addon"><i class="icon-user-follow"></i></span>
                    <input type="text" name="refcode" v-model="registrationData.refcode" class="form-control" placeholder="Enter referrer code (optional)">
                </div>
    
                <button type="submit" class="btn btn-block btn-success">
                    <i :class="spinClass"></i>
                    {{buttonText}}
                </button>
            </form>
        </div>
        <div class="card-footer p-4" v-if="step === 3 && registrationData.borrower">
            <div class="row">
                <!--<div class="col-12">-->
                <!--    <a class="btn btn-block btn-facebook" href="/auth/facebook">-->
                <!--        <span>Sign up with facebook</span>-->
                <!--    </a>-->
                <!--</div>-->
            </div>
        </div>
    </div>
</template>
<script>
    import { utilitiesMixin } from './../../mixins';

    export default {
        mixins: [utilitiesMixin],
        
        props: ['redirect', 'refcode'],
        
        data() {
            return {
                step: 1,
                registrationData: {
                    email: '',
                    password: '',
                    password_confirmation: '',
                    firstname: '',
                    midname: '',
                    lastname: '',
                    company_name: '',
                    phone: '',
                    individual: true,
                    borrower: true,
                    refcode: '',
                },
                buttonText: 'Create Account'
            };
        },
        
        mounted() {
            this.registrationData.refcode = this.refcode;  
        },
        
        methods: {
            async register() {
                this.buttonText = 'Please wait...'
                this.startLoading();
                try {
                    
                    const response = await axios.post('/register/modified', this.registrationData);
                    
                    if(response.data.status === 1) {
                        this.buttonText = 'Success! Redirecting';
                        //redirect appropriately
                        window.location = this.redirect + '?just_registered=true';
                    } else {
                        this.errorMessage = response.data.message;   
                    }
                    
                    this.buttonText = 'Create Account';
                    this.stopLoading();
                
                    
                } catch(error) {
                    this.handleApiErrors(error);
                    this.buttonText = 'Create Account';
                    this.stopLoading();
                }
            },
            
            logBorrowerLender(type) {
                switch(type) {
                    case 1:
                        this.registrationData.borrower = true;
                        this.registrationData.individual = true;
                        this.step = 3;
                        break;
                    case 2:
                        this.registrationData.borrower = false;
                        this.step = 2;
                        break;
                }
            },
            
            logIndividualCompany(type) {
                switch(type) {
                    case 1:
                        this.registrationData.individual = true;
                        break;
                    case 2:
                        this.registrationData.individual = false;
                        this.step = 3;
                        break;
                }
                this.step = 3;
                return;
            },
        }
    };
</script>