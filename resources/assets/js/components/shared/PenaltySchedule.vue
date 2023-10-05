<template>

    <div>
        <div class="card-header">
            <h4 class="card-title">Total Amount Owed : {{formatAsCurrency(amountOwed)}}</h4>
        </div>
        <br>
         <div class="tab-pane fade show active" id="entries" role="tabpanel" aria-labelledby="entries-tab">
               <table class="table table-responsive">
                    <tbody >
                        <tr v-for="(entry, index) in entries" :key="index">
                            <td>₦{{entry.amount}}</td>
                            <td>{{entry.desc}}</td>
                            <td>{{entry.created_at}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </div>
</template>

<script>
import { utilitiesMixin } from './../../mixins';

export default {
    mixins: [utilitiesMixin],
    props : {
        reference : {
            type : String
        }
    },

    data() {
        return  {

            entries : [],
            amountOwed : 0,
            upload : {amount : '', desc:'', status: 1, loan_ref : this.reference}
        };
    },

    mounted() {

        this.fetchLoanPenaltyDetails();
    },

    methods: {

        async fetchLoanPenaltyDetails(){

            this.startLoading();
            await axios.get(`/ucnull/manage-penalty/details/${this.reference}`).then((res)=> {
                this.entries = res.data.entries;
                this.amountOwed = res.data.amountOwed;
            }).catch((e)=> this.alertError(e.response))
            this.stopLoading();
        },

       
    }
}
</script>