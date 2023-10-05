<template>
    <div>
        <form @submit.prevent="handleSubmit">
            <br><br>
            <div class="row">            
                <div class="col-md-6">				
                    <label for="from"><b>Group Name</b></label>
                    <input type="text" class="form-control" v-model="group_name" id="group_name" >
                </div>

                <div class="col-md-6">
                <div  v-if=" groupedBorrowersnames.length > 0"  class="serachusers">
                    <span class="userinde" v-for="(user,index) in groupedBorrowersnames" :key="index">
                        <span @click="removeuser(index)">X</span>
                        {{user}}
                    </span>
                    
                </div>

                    <label for="from"><b>Select Borrowers</b></label>
                    <input type="text" name="user_query" v-model="query" class="form-control" placeholder="Search Borrower Name">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between " v-for="(user,index) in users"  :key="index" >

                            <!-- <input type="checkbox"  @click="handleBorrowersArr(user.reference)" > -->
                            <span  style="cursor:pointer; display:block" @click="handleBorrowersArr(user.reference, user.name)"> 

                            <span>{{user.name}} </span> <span> {{user.reference}}</span>
                            </span>
                            
                        </li>					
                    </ul> 
                </div> 
            </div>

            <div class="row">
                <div class="mt-3">
                    <button class="btn btn-info float-right" :disabled="loading"><i :class="buttonClass"></i>Add To Group</button>
                </div>
            </div>  
        </form>         

    </div>
</template>
<script>
import { utilitiesMixin } from "../../mixins";
import {mapGetters} from 'vuex';
 
 export default{
    mixins: [utilitiesMixin],
    data(){
		return {
            query : '',
            laonstatus:false,
			groupedBorrowers:[],
			groupedBorrowersnames:[],
			group_name: "",
			loading: false,
			buttonClass: {
                fa: true,
                "fa-check-circle-o": true,
                "fa-spin": false,
                "fa-spinner": false
            },            
		};
	},


    watch : {
            query : {
                handler: _.debounce(function(){
                    this.searchBorrowers();
                },0)
            }
        },

    computed : {
        users() {
            return this.RepaymentBorrowers.filter((user) => {
                return (
                    user.name
                        .toLowerCase()
                        .indexOf(this.query.toLowerCase()) != -1 
                   
                );
            });
        },
        ...mapGetters(['RepaymentBorrowers'])
    },

    methods:{
        async handleSubmit(){
            if(this.group_name == '') {                    
                return this.alertError('Please enter a valid Group Name');
            }

            if(this.groupedBorrowers == '') {
                   return this.alertError('Please Add Borrowers To This Group');
            }
	 	   let borrowersParams = {
                    groupedBorrowers : this.groupedBorrowers,
                    group_name: this.group_name
                }
                await axios.post('/ucnull/loans/group/create', borrowersParams).then((res)=>{
                  this.startLoading();
                    this.alertSuccess(res.data.message);
                    this.stopLoading();
                }).catch(error => {
                    this.alertError(error);
				})
		},
        removeuser(index){
            if(this.groupedBorrowers[index]){

                this.groupedBorrowers.splice(index, 1);
                this.groupedBorrowersnames.splice(index, 1);

            }

        },
        handleBorrowersArr(reference, name){
                // if reference is in array we remove it from the array
                // else we add it to the array
                if(this.groupedBorrowers.includes(reference)){
                    let index = this.groupedBorrowers.indexOf(reference);
                    this.groupedBorrowers.splice(index, 1); 
                    this.groupedBorrowersnames.splice(index, 1); 
                }else{
                    this.groupedBorrowers.push(reference)                      
                    this.groupedBorrowersnames.push(name)                      
                    
                }
        },

        searchBorrowers() {
      
            let userQuery = this.query;
            this.$store.dispatch('searchRepaymentBorrowers', {
                userQuery
            })
        },
        
		startLoading() {
                this.loading = true;
                this.buttonClass['fa-spinner'] = true;
                this.buttonClass['fa-spin'] = true;
                this.buttonClass['fa-check-circle-o'] = false;
            },
            
        stopLoading() {
                this.loading = false;
                this.buttonClass['fa-spinner'] = false;
                this.buttonClass['fa-spin'] = false;
                this.buttonClass['fa-check-circle-o'] = true;
         }
    }

 };
</script>


<style scoped>
.serachusers{
 border: 1px solid #eee;
padding:10px;
}
.userinde{
    padding:10px;
    border: 1px solid #eee;
    border-radius: 5px;
    position: relative;
    display: inline-block;
    margin:5px;
}
.userinde span{
    position: absolute;
    top: -2px;
    right: -2px;
    font-size: 15px;
    cursor: pointer;
}
</style>