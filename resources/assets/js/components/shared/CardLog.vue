<template>
    <div>

        <div class="row">
            <div class="col-sm-12" style="display:inline-block">

                <form @submit.prevent="getLog">

                    <div class="form-row">

                        <button type="submit" name="submit" class="col-md-2 btn btn-primary btn-sm"> <i :class="spinClass"></i> {{searchButton}}</button>

                    </div>
                    
                </form>

            </div>

             <div class="card-body">

                            <table class="table table-responsive-sm table-hover table-outline mb-0" >

                                <thead class="thead-light">

                                    <tr>
                                        <th>Title</th>
                                        <th>Message</th>
                                   
                                        <th>Status Text</th>
                                        <th>Date</th>
                                        
                                    </tr>

                                </thead>

                                <tbody v-if="logs.length > 0">
                                  
                                    <tr v-for="(log,index) in logs" :key="index">
                                        <td>{{log.title}}</td>
                                        <td>{{log.message}}</td>
                                        <td>{{log.status}}</td>
                                        <td>{{log.created_at}}</td>
                                    </tr>
                                  
                                </tbody>

                                <tbody v-else>

                                    <tr>
                                        <td colspan="3" class="text-center"> No Transactions available</td>
                                    </tr>

                                </tbody>

                            </table>
                        </div>
        </div>
    </div>
</template>
<script>
import { utilitiesMixin } from './../../mixins';

export default {

    mixins: [utilitiesMixin],

    props  : {

        loan : {
            type :Object
        }
    },


    data(){

        return {

            searchButton : 'Get Log',
            logs : []

        }
    },
    created(){

    },

    watch: {

    },

    computed : {


    },


    methods : {

        async getLog(){

            this.startLoading();

            this.searchButton = 'Searching..';

            await axios.get(`/ucnull/loans/sweep/logs/${this.loan.reference}`).then((res)=>{
                this.logs = res.data;
            }).catch((e)=> {
                this.alertError(e.response.data);
            })
            
           this.stopLoading();

        },

        
    }
}
</script>

<style>
@media (min-width: 576px){

    .modal-dialog {

        max-width: 700px;
        margin: 30px auto;

    }
}

.modal-dialog {
    margin-top : 5px;
}

.modal .modal-dialog .modal-content .modal-body {
    padding-top:30px;
}

tr {
    cursor : pointer;
}

#transactions-table  tr td , #transactions-table tr th{

    font-size: 12px;
}

</style>