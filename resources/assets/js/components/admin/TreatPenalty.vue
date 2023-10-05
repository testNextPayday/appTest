<template>

    <div>
        <div class="card-header">
            <h4 class="card-title" v-if="amountOwed > 0">Excess Balance : {{formatAsCurrency(amountOwed)}}</h4>
             <h4 class="card-title" v-else> Penalty Balance: {{formatAsCurrency(amountOwed)}}</h4>
        </div>
        <br>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="entries-tab" data-toggle="tab" href="#entries" role="tab" aria-controls="entries" aria-selected="true">Entries</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="upload-tab" data-toggle="tab" href="#upload" role="tab" aria-controls="upload" aria-selected="false">Upload Payments</a>
            </li>
           
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="entries" role="tabpanel" aria-labelledby="entries-tab">
               <table class="table table-responsive">
                   <thead>
                       <tr>
                           <th>S/N</th>
                           <th>Description</th>
                           <th>Date</th>
                           <th>Dr.</th>
                           <th>Cr.</th>
                       </tr>
                   </thead>
                    <tbody >
                        <tr v-for="(entry, index) in entries" :key="index">
                            <td>{{parseInt(index) + 1}}</td>
                            <td>{{entry.description}}</td>
                            <td>{{entry.collection_date}}</td>
                            <td>₦{{entry.direction == 2 ? formatAsCurrency(entry.amount) : '0.00'}}</td>
                            <td>₦{{entry.direction == 1 ? formatAsCurrency(entry.amount) : '0.00'}}</td>
                        </tr>
                        <!-- <tr>
                            <td></td>
                            <td>Total :</td>
                            <td></td>
                            <td></td>
                            <td><h4 class="card-title" v-if="amountOwed > 0">{{formatAsCurrency(totalSum)}}</h4></td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade px-3 py-5" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                <form @submit.prevent="saveEntry" style="width:70%;margin:auto;">

                    <div class="form-group">
                        <label class="form-control-label">Amount</label>
                        <input type="number" step="0.001" class="form-control" v-model="upload.amount">
                    </div>

                    <div class="form-group">
                        <label class="form-control-label">Describe Transaction</label>
                        <input type="text" v-model="upload.desc" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-control-label">Debit or Credit</label>
                        <select name="status" class="form-control" v-model="upload.direction">
                            <option value="2">Debit</option>
                            <option value="1">Credit</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-control-label">Transaction Date</label>
                        <input type="date" v-model="upload.date" class="form-control" name="trnx_date">
                    </div>

                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" > <i :class="spinClass"></i> Update</button>
                    </div>


                </form>
            </div>
            <!-- <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">...</div> -->
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
            upload : {amount : '', desc:'', direction: 1, loan_ref : this.reference, date : ''}
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
                this.totalSum = res.data.sumDue;
            }).catch((e)=> this.alertError(e.response))
            this.stopLoading();
        },

        async cancelEntry(id) {
            this.startLoading();
            await axios.post(`/ucnull/manage-penalty/cancel/entry/${id}`).then((res)=> {
                this.fetchLoanPenaltyDetails();
            }).catch((e)=> this.alertError(e.response))
            this.stopLoading();
        },

        async saveEntry() {
            this.startLoading();
            const req = this.upload;
            await axios.post(`/ucnull/manage-penalty/save/entry`, req).then((res)=> {
                this.fetchLoanPenaltyDetails();
            }).catch((e)=> this.alertError(e.response))
            this.stopLoading();
        },
    }
}
</script>