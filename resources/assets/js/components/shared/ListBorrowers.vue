<template>

    <div class="js-search-hints" @mouseenter="$emit('focussed',$event.target.value)" @mouseleave="$emit('focussedout',$event.target.value)">

        <ul class="nav"  v-if="borrowers.length > 0">

            <li class="nav-item" v-for="(borrower,index) in borrowers" :key="index"  >

                <!-- <a class="nav-link loan-collapser" data-toggle="collapse" :href="'#'+index" aria-expanded="false" :aria-controls="index" style="display:inline-block">
                   
                   <i class="fa fa-chevron-right fa-xs" style="font-size:0.7rem" v-if="borrower.loans.length > 0"></i> 
                </a> -->

                <span style="padding-left:5px;" v-if="borrower.loan"><a style="display:inline-block" class='nav-link' :href="'/ucnull/loans/view/'+borrower.loan.reference">{{borrower.name}} <i v-if="borrower.payroll_id">- ({{borrower.payroll_id}})</i></a></span>

                <!-- <div class="collapse" :id="index">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item" v-for="(loan,index2) in borrower.loans" :key="index2">
                            <a class="nav-link" :href="'/ucnull/loans/view/'+loan.reference"> {{loan.reference}} ({{loan.amount}}) -{{loan.duration}} months</a>
                        </li>
                    </ul>
                </div> -->

            </li>
        </ul>

        <div v-else id="borrower-list-else">
            <h5> <i class="fa fa-search"></i>  No borrower found, Enter query in search</h5>
        </div>
    </div>
</template>
<script>
export default {
    props : ['borrowers'],
    data(){
        return{

        };
    },
    methods : {
        notifyParent(event){
            console.log('emitting event')
            this.$emit('focussed')
        }
    }
}
</script>
<style>

    #borrower-list-else {
        width: 100%;
        height: inherit;
        padding-top: 10px;
        color:grey;
        text-align: center;
    }
    .js-search-hints {
        background-color: white;
        border: 1px solid grey;
        border-bottom-right-radius: 12px;
        height:auto;
        max-height: 200px;
        overflow-y: scroll;
        position: absolute;
        top: 97%;
        width: 49%;
    }
    .js-search-hints .nav {
        display: flex;
        flex-direction: column !important;
        align-items: flex-start;
        flex-wrap: nowrap;
    }
    .js-search-hints .nav .nav-item .nav-link {
       display: flex;
       white-space: nowrap;
    }
    .loan-collapser {
      
        margin-left: 15px;
    }
    .js-search-hints .nav .collapse {
        margin-left: 1.3rem;
    }

    
</style>
