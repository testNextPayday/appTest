<template>

    <div>

       
        <div class="" style="margin-bottom : 30px;">

            <div class="pull-right">

                <button class="btn btn-sm btn-primary" @click="StopSweep">
                    Stop Sweep
                </button>

                <button class="btn btn-sm btn-danger" @click="StartSweep">
                    <i :class="spinClass"></i>
                    Start Sweep
                </button>

            </div>



            <div>

                <h3>{{plans.length}} Plans Due </h3>

                <div class="sweep-progress" v-if="currentIndex">
                   Sweeping {{currentIndex}} of {{plans.length}} <i class="fa fa-spinner fa-spin" v-if="!stopped"></i>
                </div>

                <div v-for="(item, index) in Object.values(batchStatus)" :key="index" style="color:darkblue;">
                    {{item}}
                </div>

                <p class="text-danger">Note :  Sweeper makes 3 sweeps per EMI</p>

            </div>

        </div>

        <!-- This is the div that holds our plans -->
        <div>
            <table class="table table-responsive" id="plans-list">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Buffers</th>
                        <th>Borrower</th>
                        <th>Installment No</th>
                        <th>EMI</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Card Tries</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(plan, index) in plans" :key="index">
                        <td>{{index + 1}}</td>
                        <td><buffer-status :buffers="plan.buffers"></buffer-status></td>
                        <td>{{plan.loan.user.name}}</td>
                        <td>{{plan.month_no}}</td>
                        <td>{{formatAsCurrency(plan.emi)}}</td>
                        <td>{{plan.payday}}</td>
                        <td>
                            <span v-if="plan.status == 1">Paid</span>
                            <span v-else>Unpaid</span>
                        </td>
                        <td>{{plan.card_tries}}</td>
                    </tr>
                </tbody>
            </table>

            <div class="console" v-if="messages.length">

                <div v-for="(msg, index) in messages" :key="index">
                    <span v-if="msg.type == 'success'" class="text-success"> {{msg.data}} </span>
                        <span v-else class="text-danger"> {{msg.data}} </span>
                </div>

            </div>
        </div>
    </div>

</template>

<script>

import { utilitiesMixin } from './../../mixins';

export default {

    mixins: [utilitiesMixin],

    props : {
        batch : {
            type : Number,
            required: true
        }
    },

    data() {

        return  {

            stopped : false,
            plans : [],
            currentIndex : 0,
            messages : [],
            batchStatus : {failed : '0  has failed', success : '0 has compledted successfully'},
           
        };
    },

    async created() {

        await this.GetSweepablePlans();



         $("#plans-list").DataTable({
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

    methods : {

        async GetSweepablePlans() {

            await axios.get(`/ucnull/managed-loans-sweeps`).then((res)=> {

                this.plans = res.data;

            }).catch((e)=> {

                this.logError(e.response.data);
            });
        },
        async GetBatchStatus() {

            await axios.get(`/ucnull/managed-loans-sweeps/status`).then((res)=> {

                this.batchStatus = res.data;

            }).catch((e)=> {

                this.logError(e.response.data);
            });
        },
        

        async StartSweep() {

            console.log('Starting sweep')

            this.startLoading();

            this.stopped  = false;

            // initiate the start of the sweeping
            for (var index = this.currentIndex; index < this.plans.length; index++)
            {
               
                if (this.stopped) {
                    console.log('Stoping sweep')
                    break;
                }
                this.currentIndex = index + 1;

                var plan = this.plans[index]; 

                await axios.post(`/ucnull/managed-loans-sweeps/plan/${plan.id}`).then((res)=>{

                    this.logSuccess(res.data);

                }).catch((e)=>{

                    this.logError(e.response.data);
                })

                // get batch status

                this.GetBatchStatus()
                
            }

            // indicate that it has stopped
            this.stopped  = true;

            this.stopLoading();

             console.log('Stoping sweep')

        },

        StopSweep() {

            this.stopped = true;
            this.logError('Sweep has been stopped');
        },

        logSuccess(data) {

            var msg = {data: data, type : 'success'};
            this.messages.push(msg);
        },

        logError(data) {

            var msg = {data: data, type : 'error'};
            this.messages.push(msg);
        }
    }
}
</script>

<style scoped>

    .console {

        height: 200px;
        overflow: scroll;
        width: 100%;
        margin-top: 40px;
        overflow-x: hidden;
        border: 0.5px solid black;
        padding: 10px;
        background-color: beige;
    }

    .sweep-progress {

        display: inline-block;
        height: 1.3rem;
        overflow: hidden;
        font-size: 0.85rem;
        background-color: #e9ecef;
        border-radius: 3px;
        text-align: center;
        color: green;
    }

</style>