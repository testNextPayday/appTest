<template>

    <div style="width:100%">

        
        <form class="form-inline my-2 my-lg-0">

            <div class="input-group">

                 <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" v-model="query" @focus="focussedHandler" @blur="blurHandler">

                <div class="input-group-addon">

                    <select v-model="type" class="form-control">
                        <option value="borrowers">By Name</option>
                        <option value="payroll_id">By Payroll ID</option>
                    </select>

                </div>  
            </div>
           
            <!-- <button class="btn btn-outline-success my-2 my-sm-0" type="submit" @click.prevent="searchBorrowers" @keyup="searchBorrowers">Search</button> -->
        </form>

        <list-borrowers :borrowers="borrowers" v-if="!hideList" @focussed="focussedListHandler" @focussedout="blurListHandler"></list-borrowers>
    </div>
  
</template>
<script>
    import {mapGetters} from 'vuex';
    export default {
        data(){
            return {
                query : '',
                type : 'payroll_id',
                hideList:true,
                focussed : false,
                viewingList : false,
            };
        },
        mounted(){
            //this.$store.dispatch('getBorrowers')
            // window.SearchEcho.channel('searchBorrower').listen('.borrowerResults',(e)=>{
            //     console.log(e);
            //     this.$store.commit('SEARCH_BORROWERS',e.borrowers);
            // })
        },
        watch : {

            query : {
                handler: _.debounce(function(){
                    this.searchBorrowers();
                },0)
            },

            type : function(newValue,oldValue){
                return (newValue != oldValue) ? this.$store.commit('EMPTY_BORROWERS'): null; 
            }
             
        },
        methods : {
           
            searchBorrowers(){

                let type = this.type;
                let query = this.query;
                this.$store.dispatch('searchBorrowers',{
                    query,
                    type
                })
            },
            focussedHandler(){
                this.focussed = true;
                this.hideList = false;
            },
            blurHandler(){
                this.hideList = !this.viewingList
            },
            focussedListHandler(){
                this.viewingList = true;
            },
            blurListHandler(){
                this.viewingList = false;
            }
        },
        computed: {
            borrowers_list(){
                return _.chunk(this.borrowers,4);
            },
            ...mapGetters([
                'borrowers'
            ])
        }
    }
</script>
<style scoped>
   
    .input-group {
        width:100%;
        display: flex;
        flex-direction: row;
    }
    .forn-control {
        width: 100%;
    }

    input[type="search"]{
        
        height:40px;
        flex:3;
    }
    .input-group-addon {
        flex : 1;
    }
</style>

