<template>
    <div>
        <div v-if="loading" style="padding : 5px; color:green">
            <i class="fa fa-spin fa-2x fa-spinner"></i> <span style="font-size: 1.3rem;">Please wait</span> 
        </div>

        <div v-else>
            
            <span class="alert alert-success" v-if="successMsg">{{successMsg}}</span>
            <br>
            <br>

            <div v-if="statementLink">
                <a target="_blank" :href="statementLink">{{successMsg}}</a>
            </div>
        </div>
       
    </div>
</template>

<script>
import {utilitiesMixin} from '../../mixins';
export default {
    name : 'mono-statement',
    mixins : [utilitiesMixin],
    props : ['reference', 'monokey'],

    data() {
        return {
            
            monoID : '',
            statementLink : '',
            successMsg : '',
            errMsg : '',
            loading : false
        };
    },

    mounted(){
        // check if this user already has a mono id
        // if id check if the id is valid if not valid re-auth
        this.checkUserAlreadyMonofied();
    },

    methods : {

        async checkUserAlreadyMonofied() {
            this.loading = true;
            await axios.get(`/users/mono/checkMonoStatus`).then((res)=> {
                if (res.data.data && res.data.data.id) {
                   this.attemptStatementRetrieval(res.data.data.id);
                } else {
                    this.bootUpMonoWidget();
                }
            }).catch((e)=> {
                console.log(e);
                console.log(e.response);
               this.alertError(e.response.data.message);
            })
            this.loading = false;
        },

        async attemptStatementRetrieval(id) {
             this.loading = true;
            await axios.get(`/users/mono/statement`,{
                params : {
                    monoId : id
                }
            }).then((res)=> {
                if (res.data.status) {
                    this.statementLink = res.data.data.statementPDF;
                    this.successMsg = res.data.message;
                    this.$emit('cleared', this.statementLink)
                }
            }).catch((e)=> {
                this.alertError(e.response.data.message);
            })
             this.loading = false;
        },

        bootUpMonoWidget() {

            var connect;
            var $vm = this;
            var config = {
                key: $vm.monokey,
                onSuccess: function (response) {
                    var code = response.code;
                    $vm.getID(code);
                },
                onClose: function () {
                    console.log('user closed the widget.')
                }
            };

            connect = new Connect(config);
            connect.setup();

            connect.open();
        },

        async getID(code) {
             this.loading = true;
             const req = {auth_code : code};
            await axios.post(`/users/mono/authentication`, req).then((res)=> {
                if (res.data.status) {
                    let id = res.data.data.id
                    this.attemptStatementRetrieval(id)
                }
            }).catch((e)=> {
                this.alertError(e.response.data.message);
                // Try again
                if(e.response.data.code == 'bank_failure') {
                    this.bootUpMonoWidget();
                }
                
            })
             this.loading = false;
        },
    }
    
}
</script>