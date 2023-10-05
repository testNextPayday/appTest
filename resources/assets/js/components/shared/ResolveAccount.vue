<template>
    <div>

        <form @submit.prevent="resolveAccountNumber">

            <div class="form-group">
                <label class="form-control-label">Bank</label>
                <select class="form-control" v-model="bankCode">
                    <option v-for="(bank, index) in banks" :key="index" :value="index">{{bank}}</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-control-label">Account Number</label>
                <input class="form-control" type="text" v-model="accountNumber">
            </div>

            <div class="form-group">
                <label class="form-control-label">Account Name</label>
                <input class="form-control" type="text" v-model="accountName" disabled="disabled">
            </div>

            <p v-if="isAccountResolved" class="text-success"> Account Resolved Successfully</p>
            <p v-if="resolveAccountNumberError" class="text-danger"> {{resolveAccountNumberError}}</p>


            <div class="form-group">
                <button type="submit" class="btn btn-primary" > <i :class="spinClass"></i>Verify Account</button>
            </div>


        </form>
    </div>
</template>

<script>
import { utilitiesMixin } from './../../mixins';

export default {

    mixins: [utilitiesMixin],

    name: 'resolve-account',
    props : {

        bankdetails : {
            type : Object,
            required : true
        },

        banks : {
            type : Object,
            required : true
        }
    },

    data(){

        return {
            accountNumber : this.bankdetails.account_number,
            bankCode : this.bankdetails.bank_code,
            accountName : '',
            isAccountResolved : false,
            resolveAccountNumberError : ''
        };
    },

    methods: {

         async resolveAccountNumber() {
        
            this.startLoading();

            this.isAccountResolved = false;

            this.resolveAccountNumberError = '';

            this.accountName = '';

            const req = {bank_code : this.bankCode, account_number : this.accountNumber};

            await axios.get('/resolve/account/number', { params : req}).then((res)=> {

                let data  = res.data;
                this.accountName = data.data.account_name;

                this.isAccountResolved = true;
               
            }).catch((err)=> {

                this.resolveAccountNumberError = err.response.data;
            })

            this.stopLoading();

            
        },

    }
}
</script>