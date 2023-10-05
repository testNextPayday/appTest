<template>
    <div>

        <form @submit.prevent="resolveCardDetails">       


            <p v-if="isCardResolved" class="text-success"> Card Resolved Successfully <br>{{resolveMessage}}<br>
                
            </p>
            <p v-if="resolveCardError" class="text-danger"> {{resolveCardError}}</p>


            <div class="form-group">
                <button type="submit" class="btn btn-primary" > <i :class="spinClass"></i>Verify Card</button>
            </div>


        </form>
    </div>
</template>

<script>
import { utilitiesMixin } from './../../mixins';

export default {

    mixins: [utilitiesMixin],

    name: 'resolve-card',
    props : {

        user_reference : {
            type : String,
            required : true
        },
        
    },

    data(){

        return {
            userReference : this.user_reference,            
            isCardResolved : false,
            resolveMessage: '',
            resolveCardError : ''
        };
    },

    methods: {

         async resolveCardDetails() {
             
            this.startLoading();

            this.isCardResolved = false;

            this.resolveCardError = '';            

            await axios.post(`/resolve/card/${this.user_reference}`).then((res)=> {
                 let data  = res.data;
                 console.log(data);
                this.resolveMessage = data.data;
                console.log(this.resolveMessage);
                this.isCardResolved = true;
               
            }).catch((err)=> {

                this.resolveCardError = err.response.message;
            })

            this.stopLoading();

            
        },

    }
}
</script>