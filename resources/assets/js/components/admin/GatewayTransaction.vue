<template>
      
        <tr :class="[status == 1 ? 'text-success' : 'text-danger']" @click="$emit('viewtransaction',$event)">

            <td v-if="showMore">
               {{transaction.description}}
            </td>

            <td v-if="showMore">
               {{transaction.collection_method}}
            </td>

            <td v-if="showMore">

                {{transaction.reference}}

            </td>

            <td>
                {{formatAsCurrency(transaction.amount)}}
            </td>

            <td>
                {{transaction.status_message}}
            </td>

            <td>
                {{transaction.created_at}}
            </td>
            <td v-if="!showMore">
                
                <transaction-actions :proptransaction="transaction"></transaction-actions>

            </td>
        </tr>
   

</template>

<script>

import { utilitiesMixin } from './../../mixins';

import TransactionActions from './TransactionActions';

export default {

    mixins: [utilitiesMixin],

    components  : {

        'transaction-actions' : TransactionActions
    },

    props : {

        transaction : {
            type : Object,
            required : true
        },

        showMore : {
            type : Boolean,
            default : false,
        }
    },

    data(){

        return {

            status : this.transaction.pay_status,
        };
    },
    
   
}
</script>