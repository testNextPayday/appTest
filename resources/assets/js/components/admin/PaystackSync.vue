<template>

    <div>

        
       

        <div class="row">
            <div class="col-md-12">
                <div class="float-right">
                    <button class="btn btn-xs btn-primary" @click="sync"> <i :class="spinClass"></i> Sync Customers ({{customers.length}}) </button>
                </div>
                <div></div>
                <table class="table table-bordered table-responsive">
                    <thead>
                        <th>Customer Code</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </thead>

                    <tbody v-if="customers.length > 0">
                        <tr v-for="(customer, index) in customers" :key="index">
                            <td>{{customer.customer_code}}</td>
                            <td>{{customer.first_name}}</td>
                            <td>{{customer.last_name}}</td>
                            <td>{{customer.email}}</td>
                            <td>{{customer.phone}}</td>
                        </tr>
                    </tbody>

                    <tbody v-else>
                        <p> No customer exists</p>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</template>

<script>

import { utilitiesMixin } from './../../mixins';

export default {
    name : 'paystack-sync',


    mixins: [utilitiesMixin],
    
    data(){

        return {

            customers : []
        };
    },

    mounted() {

        this.getCustomers();
    },
    methods : {

        async getAllCustomers() {

             this.startLoading();

            await axios.get(`/ucnull/paystack/all/profile/customers`).then((res)=>{

                this.customers = res.data.data;

                this.stopLoading();

            }).catch((e)=> {

                this.alertError(e.response.data);

                this.stopLoading();
            })


        },

        async getCustomers() {

            this.startLoading();

            await axios.get(`/ucnull/paystack/incomplete/profile/customers`).then((res)=>{

                this.customers = res.data.data;

                this.stopLoading();

            }).catch((e)=> {

                this.alertError(e.response.data);

                this.stopLoading();
            })

            
        },

        async sync() {

            this.startLoading();

            await axios.post(`/ucnull/paystack/sync`).then((res)=> {

                this.stopLoading();

                this.alertSuccess(res.data.message);

            }).catch((e)=> {

                this.stopLoading();
                this.alertError(e.response.data);
            })
        }
    }
}
</script>