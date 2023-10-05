<template>
    <div>
        <loader v-if="loading"></loader>

        <div v-else>
            <div class="row">
                <div class="col-md-4 grid-margin" v-for="(category, index) in recordStore" :key="index">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-0">{{index}}</h4>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-inline-block pt-3">
                                    <div class="d-flex">
                                        <h2 class="mb-0">₦{{category.amount}}</h2>
                                        <!-- <h2 class="mb-0">₦{{number_format($loanRequest->emi() + $loanRequest->fee(), 2)}}</h2> -->
                                        <div class="d-none d-md-flex align-items-center ml-2">
                                        <!--<i class="mdi mdi-clock text-muted"></i>-->
                                        <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                        </div>
                                    </div>
                                    <small class="text-gray">
                                        {{category.total}}
                                    </small>
                                </div>
                                <div class="d-inline-block">
                                    <div class="bg-success px-4 py-2 rounded">
                                        <i class="icon-arrow-right-circle text-white icon-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data(){
            return{
                loading : true,
                recordStore : []
            };
        },
        mounted() {
            this.getBillStatistics();
        },
        methods: {
            async getBillStatistics(){
                await axios.get('/ucnull/bills/statistics/data').then((res)=> {
                    this.recordStore = res.data;
                    this.loading = false;
                })
            }
        }
    }
</script>