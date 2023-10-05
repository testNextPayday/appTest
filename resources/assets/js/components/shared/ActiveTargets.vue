<template>
    <div class="col-md-12 grid-margin stretch-card"> 
        <div class="card">
           
            <div class="card-body">
                <h1 class="card-title text-underline">Running targets </h1>
                <div v-if="targets.length > 0" class="row">
                    <div v-for="(data, index) in targets" :key="index" style="font-family: 'Bellota', cursive; margin-top : 15px; margin-bottom:15px;" class="col-md-6">
                        <p class="mb-2 font-weight-bold">#{{data.name}}</p>
                        <p>
                            <span v-if="data.type == 'investors'">Bring Investors</span>
                            <span v-if="data.type == 'book_loans'">Book Loans </span>
                            <span>Worth ₦{{formatAsCurrency(data.target)}}</span>
                        </p>
                        <p class="text-success"> ₦{{formatAsCurrency(data.reward)}}  In <timer :starttime="data.timeLeft['startTime']" :endtime="data.timeLeft['endTime']"></timer></p>
                        <div class="progress" style="height:14px">
                            <div :class="['progress-bar progress-bar-striped progress-bar-animated',{'bg-danger': data.percentage < 50}, {'bg-success' : data.percentage == 100}]" role="progressbar" :style="'width:'+data.percentage+'%;'" :aria-valuenow="data.percentage" aria-valuemin="0" aria-valuemax="100">{{data.percentage}}%</div>
                        </div>
                    </div>
                </div>
                <div v-else><p>No Data available for target</p></div>
            </div>
        </div>
    </div>
</template>

<script>

import { utilitiesMixin } from './../../mixins';

export default {
    name : 'active-target',

     mixins: [utilitiesMixin],

    data(){
        return {
            targets : []
        };
    },

    mounted() {

        this.getTargets();
    },

    methods : {

        async getTargets() {
            
            await axios.get(`/affiliates/targets/data`).then((res)=> {
                this.targets = res.data
            });
        }
    }
}
</script>