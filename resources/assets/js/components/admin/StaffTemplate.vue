<template>

    <tr v-if="loading">
        <td colspan="6"><newton-loader></newton-loader></td>
    </tr>

    <tr v-else>
        <td>{{staff.firstname}} {{staff.lastname}} </td>
        <td> ₦{{formatAsCurrency(staff.salary)}}</td>
        <td>{{bank.account_number}}</td>
        <td>{{bank.bank_name}}</td>
        <td>
            <span v-if="bank.recipient_code">{{bank.recipient_code}}</span>
            <div v-else>
                <form @submit.prevent="createRecipient">
                    <button type="submit" class="btn btn-xs btn-primary">Create Recipient</button>
                </form>
            </div>
        </td>

        <td class="row">

            <div class="col-md-6">

                <form @submit.prevent="paySingleStaff" v-if="!showSalaryInput">
                    <button type="submit" class="btn btn-xs btn-danger">Pay Me</button>
                </form>

            </div>

            <form :class="{'form-row col-md-12' : showSalaryInput,'form-row col-md-6': !showSalaryInput }" @submit.prevent="updateSalary" v-if="showSalaryInput">

                <div class="col-md-6">
                    <input type="text" v-model="salary" class="form-control">
                </div>

                <div class="col-md-6">
                    <button type="submit" class="btn btn-xs btn-warning">Update</button>
                </div>
            </form>

            <div v-else class="col-md-6">
                <button @click="showSalaryInput = true" class="btn btn-xs badge btn-primary">Update Salary</button>
            </div>

        </td>
    </tr>
</template>

<script>

import { utilitiesMixin } from './../../mixins';

export default {

    mixins: [utilitiesMixin],

    props : {

        staff : {
            type :Object,
            required : true
        }
    },

    data(){

        return {

            salary : this.staff.salary,

            showSalaryInput : false,

            paid :  false
        };
    },

    computed : {

        bank(){

            return this.staff.banks[this.staff.banks.length - 1];
        }
    },

    methods : {

        async updateSalary(){

            this.startLoading();

            const request = {salary : this.salary };

            await axios.post(`/ucnull/payment-salaries/update/${this.staff.reference}`,request).then((res)=> {

                this.alertSuccess(res.data);

                this.$emit('staffupdate');

            }).catch((e)=> {

                this.alertError(e.response.data);
            })

            this.showSalaryInput = false;

            this.stopLoading();
        },

        async paySingleStaff(){

            this.startLoading();

            await axios.post(`/ucnull/payment-salaries/pay/staff/${this.staff.reference}`).then((res)=>{

                this.alertSuccess(res.data);

                this.paid = true;

            }).catch((e)=> {

                this.alertError(e.response.data);
            })

            this.stopLoading();
        },

        async createRecipient(){

            this.startLoading();

            await axios.post(`/ucnull/transfer-controls/create/recipient/${this.bank.id}`).then((res)=>{

                this.alertSuccess(res.data);

                 this.$emit('staffupdate');

            }).catch((e)=> {

                this.alertError(e.response.data);
            })

            this.stopLoading();
        },

    }
}
</script>