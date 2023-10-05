<template>
    <div>
        <button @click="cancelTransaction" class="btn btn-sm btn-danger"> <i :class="spinClass"></i> Cancel Transaction</button>
    </div>
</template>


<script>

import { utilitiesMixin } from "../../mixins";

export default {

    mixins: [utilitiesMixin],

    props : {
        data: {
            type : Object,
            required : true
        }
    },

    data() {
        return{

        };
    },

    methods : {

        async cancelTransaction() {

            this.startLoading();

            await axios.post(`/ucnull/wallet-transactions/cancel/${this.data.reference}`).then((res)=> {

                this.alertSuccess('Success, Another transaction has been spawned to cancel this one');

                this.$emit('successAction');

            }).catch((err)=> {

                this.alertError('An error occured');
            })

            this.stopLoading();

        },
    }
}
</script>