<template>
    <div>
        <form action method="POST" class="row p-3" id="investors_stats">

              <div class="form-group col-md-2 row">
                <label class="form-control-label">Start Date</label>
                <input type="date" name="startDate" class="form-control" v-model="investorStat.from" />
              </div>

              <div class="form-group col-md-2">
                <label class="form-control-label">End Date</label>
                <input type="date" name="endDate" class="form-control" v-model="investorStat.to" />
              </div>

               <div class="form-group col-md-3">
                <label class="form-control-label">Activity</label>
                 <select name="activity" class="form-control" v-model="investorStat.activity">
                  <option value="">All</option>
                  <option value="000">Wallet Funds</option>
                  <option value="012">Recoveries</option>
                  <option value="002">Withdrawals</option>
                  <option value="003">Loan Funds</option>
                  <option value="025">Taxes</option>
                  <option value="027">Portfolio Charge</option>
                  <option value="016">Corrective RVSL</option>
                  <option value="013">Recovery Fees</option>
                  <option value="024">Loan Voids</option>
                  <option value="033">Affiliate Loan Cost </option>
                  <option value="004">Reversed Invesments </option>
                </select>
              </div>

              <div class="form-group col-md-2">
                <label class="form-control-label"></label>
                <br />
                <button
                  type="submit"
                  name="submit"
                  class="btn btn-success"
                  @click.prevent="fetchReports"
                >
                  <i :class="spinClass"></i>
                 Submit
                </button>
              </div>
            </form>

            <div class="display">{{investorStat.info}}</div>
            <br />

            <div>
              <table class="table table-responsive" id="order-listing">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th v-if="isadmin">Action</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Direction</th>
                    <th>Owner</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(stat,index) in investorStat.data" :key="index" :class="{ 'underlined': stat.cancelled == 1 }">
                    <td>{{index + 1}}</td>
                    <td v-if="isadmin">
                        <investorTransactionButton :data="stat" @successAction="refreshPage" v-if="whiteList.includes(stat.code) && stat.cancelled == 0"></investorTransactionButton>
                        <span v-else > No action </span>
                    </td>
                    <td>{{formatAsCurrency(stat.amount)}}</td>
                    <td>{{stat.description}}</td>
                    <td>{{stat.direction == 1 ? 'INFLOW' : 'OUTFLOW'}}</td>
                    <th>{{stat.owner.name}}</th>
                    <td>{{stat.created_at}}</td>
                    <!-- <td>{{investment.collections_made}}</td>
                    <td>{{investment.collections_left}}</td> -->
                  </tr>
                </tbody>
              </table>
            </div>
    </div>
</template>

<script>
import { utilitiesMixin } from "../../mixins";

import InvestorTransactionButton from './InvestorTransactionButton';

export default {

    mixins: [utilitiesMixin],

    components : {
        InvestorTransactionButton : InvestorTransactionButton 
    },
    props : {
        investor : {
            type : Object,
            required : true
        },

        isadmin : {
            type: Boolean,
            default : false
        }
    },

    beforeUpdate : function(){
        if ( $.fn.DataTable.isDataTable('#order-listing') ) {
                $('#order-listing').DataTable().destroy()
        }
    },
    updated() {
            this.initDataTable();
    },

    data() {

        return {

            id : this.investor.id,
            investorStat: {
              name: 'investorStat',
              from: "",
              to: "",
              info: "",
              investor: this.investor.id,
              activity:'',
              code: this.code,
              data: []
            },

            whiteList : ['016', '024']
        };
    },

    methods : {

        initDataTable() {
      
            $("#order-listing").DataTable({
                aLengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
                dom: "Bfrtip",
                buttons: ["copy", "csv", "excel", "pdf"],
            
                iDisplayLength: 5,
                sPaginationType: "full_numbers",
                language: {
                search: ""
                }
            });
        },

        refreshPage() {

            //this.fetchReports();
            window.location.reload();
        },



        async fetchReports() {

            const code = '009';
            const data = this.investorStat;

            this.startLoading();

            await axios.post("/ucnull/reports", {data,code})
                .then((response) => {
                this.investorStat.info = response.data.info;
                this.investorStat.data = response.data.result;
                this.stopLoading();
                })
                .catch((e)=> {
                this.handleApiErrors(e);
            })
        }


    }
}
</script>

<style scoped>

  table tr.underlined td {
    text-decoration: line-through;
    color: grey;
}
</style>