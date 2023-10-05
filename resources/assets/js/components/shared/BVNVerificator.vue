<template>

    <div>

        <form method="POST" @submit.prevent="VerifyBvn" v-if="!user.bvn_verified">

            <div class="form-group">
                <label class="form-group-label">BVN</label>
                <input type="text" class="form-control" name="bvn" v-model="bvn" >
            </div>

            <button class="btn btn-primary"> <i :class="spinClass"></i> Verify </button>

            
        </form>

        <div v-else class="alert alert-success text-center"><p>BVN has been verified</p></div>
    </div>
    
</template>

<script>

import {utilitiesMixin} from './../../mixins';

export default {
    
    name : 'bvn-verificator',

    mixins : [utilitiesMixin],

    props : {

        user : {
            type : Object,
            required : true
        },

        bank_record : {
            type : Object,
            required : true
        }
    },

    data() {
        return {
            bvn : this.user.bvn,
            bankCode : this.bank_record.bank_code,
            accountNumber : this.bank_record.account_number
        };
    },

    methods : {

        async VerifyBvn() {

            this.startLoading();

            const request = {bvn : this.bvn, bankCode : this.bankCode, accountNumber : this.accountNumber, user_id : this.user.id};
            await axios.post(`/verify/bvn`, request).then((res)=> {

                this.alertSuccess(res.data);

                window.location.reload();

            }).catch((e)=> {

                this.alertError(e.response.data);

            })

            this.stopLoading();
        }
    }
}
</script>