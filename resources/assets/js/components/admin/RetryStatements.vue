<template>
    <div>
        <form>
            <div v-if="response">{{response}}</div>
            <div class="form-group">
                <select v-model="ticketNo" class="form-control">
                    <option v-for="(request, index) in requests" :key="index" :value="request.ticket_no"> {{request.user.name}}</option>
                </select>
            </div>

            <div class="form-group">
                <button class="btn btn-primary" v-if="!canRetrieve" @click.prevent="checkStatementRequestByTicket"> <i :class="spinClass"></i> Retry </button>
                 <button class="btn btn-danger" v-if="canRetrieve" @click.prevent="retrieveBankStatement"> <i :class="spinClass"></i> Retry </button>
            </div>
        </form>
    </div>
</template>

<script>
import {utilitiesMixin} from './../../mixins';
export default {
    props : ['requests'],
    mixins : [utilitiesMixin],

    data() {
        return {
            ticketNo : '',
            pdfDocLink : '',
            response : '',
            canRetrieve : false
        };
    },

    methods : {
        async checkStatementRequestByTicket() {

                this.startLoading();
                this.resposne = '';
                await axios.post(`/bank-statement/checkRequest/ticket/${this.ticketNo}`).then((res)=> {
                   
                   if (res.data.status == "00") {

                       let result = res.data.result; 

                       if (result && (result.status == "Error" || result.status == "Insfund")) {
                           throw result.feedback;
                       }
                      this.canRetrieve  = true;
                   }else {

                   }
                }).catch((e)=> {

                    let response = e.response;
                    this.response = response;
                
                });

                this.stopLoading();
        },

         async retrieveBankStatement() {

                this.startLoading();

                await axios.get(`/bank-statement/retrieve-statement/${this.ticketNo}`).then((res)=> {
                   

                    if (res.data.status == "00") {

                        this.pdfDocLink = res.data.result;
                    }


                }).catch((e)=> {
                    
                    this.response = e.response;

                    this.canRetrieve = false;
                });

                this.stopLoading();
            },

    }


}
</script>