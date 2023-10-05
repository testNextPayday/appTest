<template>

    <div>

        <div class="" v-if="errors.length">
            <ul>
                <li class="text-danger" v-for="(error, index) in errors" :key='index'>{{error}}</li>
            </ul>
        </div>
              
        <div class="text-success" v-if="successText">
            <ul><li>{{ successText }}</li></ul>
        </div>
       
        <div v-if="page == 1">

            <h3 class="text-muted strong text-center">Reset Password</h3>
            <p class="text-muted text-center">Enter Account Details</p>
        
                            
            <form class="form-horizontal" method="POST" @submit.prevent="SendToken">
        
            
                <div class="input-group mb-3">

                    <span class="input-group-addon">Phone</span>
                    <input type="text" name="phone" class="form-control" required v-model="phone" placeholder="phone">
                    
                </div>  
            
                <div class="row">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary px-4"><i :class="spinClass"></i> Send Password Token</button>
                    </div>
                
                </div>
            </form>

        </div>


        <div v-if="page == 2">

            <h3 class="text-muted strong text-center">Reset Password</h3>
            <p class="text-muted text-center">Verify Token</p>
        
                            
            <form class="form-horizontal" method="POST" @submit.prevent="VerifyToken">
        
            
                <div class="input-group mb-3">
                     <span class="input-group-addon">Phone</span>
                    <input type="text" name="phone" class="form-control" required v-model="phone" placeholder="phone">
                    
                </div>  

                <div class="input-group mb-3">
                     <span class="input-group-addon">Token</span>
                    <input type="text" name="token" class="form-control" required v-model="token" placeholder="token">
                    
                </div>  
            
                <div class="row">

                    <div class="col-6">
                        <button type="submit" class="btn btn-primary px-4"><i :class="spinClass"></i> Verify Token</button>
                    </div>
                
                </div>
            </form>

        </div>


         <div v-if="page == 3">

            <h3 class="text-muted strong text-center">Reset Password</h3>
            <p class="text-muted text-center">Set Password</p>
        
                            
            <form class="form-horizontal" method="POST" @submit.prevent="SetPassword">
        
            
                <div class="input-group mb-3">
                       <span class="input-group-addon">Password</span>
                    <input type="password" name="password" class="form-control" required v-model="password" >
                    
                </div>  

                <div class="input-group mb-3">
                       <span class="input-group-addon">Confirm</span>
                    <input type="password" name="password_confirmation" class="form-control" required v-model="password_confirmation" >
                    
                </div>  
            
                <div class="row">

                    <div class="col-6">
                        <button type="submit" class="btn btn-primary px-4"><i :class="spinClass"></i> Set Password</button>
                    </div>
                
                </div>
            </form>

        </div>


        <div class="page-footer mt-2">
           
            <a class=" btn-link" @click.prevent="page -= 1" v-if="page > 1" href="#"><i class="fa fa-chevron-left"></i> Back</a>

        </div>
              

    </div>
         
</template>

<script>
import { utilitiesMixin } from './../../mixins';

export default {

    mixins: [utilitiesMixin],

    data(){

        return {
            phone : '',
            token : '',
            password : '',
            password_confirmation : '',
            errors : [],
            successText : '',
            page : 1
        };
    },

    methods : {

        async SetPassword(){

            this.startLoading();

            this.ClearMessages();
            const request = {

                phone : this.phone, 
                password : this.password,
                password_confirmation : this.password_confirmation
            };
            
            await axios.post('/api/v1/npd/password/create', request).then((res) => {

                this.successText  = res.data['message'];

                setTimeout(()=> location.href = 'https://nextpayday.ng/login', 1500);

            }).catch((e)=> {

                
               this.ReportErrorMessage(e);

            })

            this.stopLoading();
        },

        async VerifyToken(){

            this.startLoading();

            this.ClearMessages();
            const request = {phone : this.phone, token : this.token};
            
            await axios.post('/api/v1/npd/confirm/reset/code', request).then((res) => {

                this.successText  = res.data['message'];

                setTimeout(()=> this.page += 1, 1500);

            }).catch((e)=> {

               
               this.ReportErrorMessage(e);

            })

            this.stopLoading();
        },

        async SendToken(){

           this.startLoading();

           this.ClearMessages();

            const request = {phone : this.phone};
            
            await axios.post('/api/v1/npd/password/reset/code', request).then((res) => {

                this.successText  = res.data['message'];

                setTimeout(()=> this.page += 1, 1500);

            }).catch((e)=> {

                
                this.ReportErrorMessage(e);

            })

            this.stopLoading();
        },

        ClearMessages(){

            this.errors = [];
            this.successText = '';
        },

        ReportErrorMessage(e){

            var msg = e.response.data['message'];

            console.log(msg);

            if (typeof(msg) === 'string') {

                this.errors.push(msg);
            }

            if ( typeof(msg) == 'object') {

                var list = Object.values(msg);

                list.forEach((item)=> this.errors.push(item[0]));
            }
        }
    }
}
</script>

<style scoped>

ul li {
    list-style-type: none;
    font-size: 90%;
}
</style>