<template>

    <div>

        <div v-if="view == 'list'">

            <div v-if="isCreatingTarget">

                <h4>SET TARGET</h4>
                <br>

                <form @submit.prevent="createTarget" method="POST">

                    <div class="row">

                        <div class="form-group col-md-3">
                            <label class="form-control-label">Target Name</label>
                            <input type="text" v-model="newTarget.name" class="form-control"> 
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-control-label">No of Days</label>
                            <input type="number" v-model="newTarget.days" class="form-control"> 
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-control-label">Target Type</label>
                            <select  v-model="newTarget.type" class="form-control">
                                <option value="investors">Bring in Investors</option>
                                <option value="book_loans">Book Loans</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-control-label">Category</label>
                            <select  v-model="newTarget.category" class="form-control">
                                <option value="all">All Active Affiliates</option>
                                <option value="selective">I will select </option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-control-label">Target(Amount ₦)</label>
                            <input type="number" v-model="newTarget.target" class="form-control"> 
                        </div>

                    
                        <div class="form-group col-md-3">
                            <label class="form-control-label">Reward(Amount ₦)</label>
                            <input type="number" v-model="newTarget.reward" class="form-control"> 
                        </div>
                    </div>

                    <hr>

                    <div class="row" v-if="newTarget.category == 'selective'">

                        <div class="form-group col-md-8 ">
                            <label class="form-control-label" for="ajax"> Choose target Affiliates</label>
                            <multiselect v-model="newTarget.affiliates" id="ajax" label="name" track-by="reference" placeholder="Type to search" open-direction="bottom" :options="affiliates" :multiple="true" :searchable="true" :loading="isLoading" :internal-search="false" :clear-on-select="false" :close-on-select="false" :options-limit="300" :limit="3" :limit-text="limitText" :max-height="600" :show-no-results="false" :hide-selected="true" @search-change="asyncFind">
                                <template slot="tag" slot-scope="{ option, remove }"><span class="custom__tag"><span>{{ option.name }}</span><span class="custom__remove" @click="remove(option)">❌</span></span></template>
                                <template slot="clear" slot-scope="props">
                                <div class="multiselect__clear" v-if="newTarget.affiliates.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                                </template><span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
                            </multiselect>
                            <!-- <pre class="language-json"><code>{{ newTarget.affiliates  }}</code></pre> -->
                        </div>

                    </div>


                    <div class="row">

                        <div class="form-group col-md-4">
                            <button class="btn btn-primary">
                                <i :class="spinClass"></i> Save
                            </button>
                        </div>
                    </div>
                

                </form>

            </div>


            <div>

                <div class="card">

                    <div class="card-header">
                        Targets
                        <div class="col-sm-3" style="display:inline-block">
                            <form >
                                <input class="form-control" v-model="search"  placeholder="Enter Target" value="" required/>
                            </form>
                        </div>

                        <div class="float-right">
                            <button class="btn btn-primary" @click="toggleCreateTarget">
                                <i class="fa fa-toggle-up"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">

                        <table class="table table-responsive-sm table-hover table-outline mb-0" >

                            <thead class="thead-light">

                                <tr>
                                    <th>Status</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Reward</th>
                                    <th>Duration</th>
                                    
                                </tr>

                            </thead>

                            <tbody v-if="targetSearchList.length > 0">
                                
                                
                                <tr v-for="(target,index) in targetSearchList" :key="index" @click="viewTarget" :data-index="index" >
                                    
                                    <td>
                                        <span v-if="target.status == 3"><i class="fa fa-circle text-danger"></i></span>
                                        <span v-if="target.status == 2"><i class="fa fa-circle text-warning"></i></span>
                                        <span v-if="target.status == 1"><i class="fa fa-circle text-success"></i></span>
                                    </td>
                                    <td>
                                        {{target.name}}
                                    </td>
                                    <td>
                                        {{target.category}}
                                    </td>
                                    <td>
                                        {{formatAsCurrency(parseFloat(target.reward))}}
                                    </td>

                                    <td>
                                        {{target.days}}
                                    </td>
                                    
                                    
                                </tr>
                                
                                
                            </tbody>

                            

                            <tbody v-else>

                                <tr>
                                    <td colspan="3" class="text-center"> No Targets available</td>
                                </tr>

                            </tbody>

                                

                        </table>
                    </div>

                </div>

            </div>


        </div>

        <div v-else>
            <div class="row">

                <div class="col-md-6 card">

                    <div class="card-header">

                        <h4 @click="viewList" style="cursor:pointer"><i class="fa  fa-arrow-left"> </i> Back</h4>

                    </div>

                    <div class="card-body max-h-595">

                        <div class="object-header">

                            <div class="pull-right">

                                <span class="badge badge-success" v-if="currentTarget.status == 1">Active</span>

                                <span class="badge badge-danger" v-if="currentTarget.status == 0">Inactive</span>


                            </div>

                                <div><h4>{{currentTarget.name}}</h4></div>

                            <form>

                                <div class="form-group ">
                                    <label class="form-control-label">Target Name</label>
                                    <input type="text" v-model="currentTarget.name" class="form-control"> 
                                </div>

                                <div class="form-group ">
                                    <label class="form-control-label">No of Days</label>
                                    <input type="number" v-model="currentTarget.days" class="form-control"> 
                                </div>

                                <div class="form-group ">
                                    <label class="form-control-label">Target Type</label>
                                    <select  v-model="currentTarget.type" class="form-control">
                                        <option value="investors">Bring in Investors</option>
                                        <option value="book_loans">Book Loans</option>
                                    </select>
                                </div>

                                <!-- <div class="form-group ">
                                    <label class="form-control-label">Category</label>
                                    <select  v-model="currentTarget.category" class="form-control">
                                        <option value="all">All Active Affiliates</option>
                                        <option value="selective">I will select </option>
                                    </select>
                                </div> -->

                                <div class="form-group ">
                                    <label class="form-control-label">Target(Amount ₦)</label>
                                    <input type="number" v-model="currentTarget.target" class="form-control"> 
                                </div>

                    
                                <div class="form-group ">
                                    <label class="form-control-label">Reward(Amount ₦)</label>
                                    <input type="number" v-model="currentTarget.reward" class="form-control"> 
                                </div>

                                <div class="form-group text-center">
                                    <button class="btn btn-danger" @click.prevent="deleteTarget"> <i class="fa fa-trash"></i> Delete</button>
                                    <button class="btn btn-primary" @click.prevent="updateTarget"> <i class="fa fa-edit"></i> Update</button>
                                </div>
                            </form>

                        </div>

                     

                        <div class="text-center mt-3">

                            
                        </div>
                        

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="card-header">Target Metrics 

                        <div class="pull-right"><a href="#" @click="targetPerformance" class="badge badge-success">View Target</a></div>
                    </div>

                    <div class="card-body max-h-595">

                       <div v-if="targetMetrics.length > 0">
                           <div v-for="(data, index) in targetMetrics" :key="index" class="mb-3">
                                <p>{{data.name}} {{data.percentage}}%</p>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" :style="'width:'+data.percentage+'%'" :aria-valuenow="data.percentage" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                           </div>
                       </div>
                       <div v-else><p>No Data available for target</p></div>

                    </div>

                </div>

            </div>
        </div>

       
    </div>
</template>
<script>

import { utilitiesMixin } from './../../mixins';


export default {

    mixins: [utilitiesMixin],

    props : {
       
    },

    data(){

        return {
            newTarget : {
                name : '',
                days : 0,
                category : 'all',
                type : 'book_loans',
                target : 0,
                reward : 0,
                affiliates : []

            },
            isLoading: false,
            affiliates : [],
            search : '',
            isCreatingTarget : false,
            currentTarget : {},
            view : 'list',
            targetMetrics : {},
            targetSearchList : []
            
        }
    },
   

    watch: {

       search : {
           handler : _.debounce(function(){

               this.searchTargets();
           }, 0)
       },
    },

    mounted() {

        this.getAffiliates();
        this.getTargets();
    },

    computed : {

       targets : {
           get() {
               return this.targetSearchList;
           },
           set(newValue) {
               this.targetSearchList = newValue;
           }
       }

    },

    methods : {

        async createTarget() {

            this.startLoading();

            const req = this.newTarget;
            // target creation code
            await axios.post(`/ucnull/targets/store`, req).then((res)=> {

                this.alertSuccess(res.data);

                this.$store.dispatch('setTargets');

                this.stopLoading();

            }).catch((e)=> {

                this.alertError(e.response.data);

                this.stopLoading();
            })
        },

        async updateTarget() {

            this.startLoading();

            const req = this.currentTarget;

            await axios.put(`/ucnull/targets/update/${this.currentTarget.id}`, req).then((res)=> {

                this.alertSuccess(res.data);

                this.$store.dispatch('setTargets');

                this.stopLoading();

            }).catch((e)=> {

                this.alertError(e.response.data);

                this.stopLoading();
            })
        },

        async deleteTarget() {

            this.startLoading();

            await axios.delete(`/ucnull/targets/destroy/${this.currentTarget.id}`).then((res)=> {

                this.alertSuccess(res.data);

                this.$store.dispatch('setTargets');

                // change view
                this.viewList()

                this.stopLoading();

            }).catch((e)=> {

                this.alertError(e.response.data);
                
                this.stopLoading();
            })
        },

        async getTargets() {

            await this.$store.dispatch('setTargets');
            this.targets = this.$store.state.targets;
            
        },

        searchTargets() {

            this.targets = this.$store.state.targets.filter((target)=>target.name.toUpperCase().indexOf(this.search.toUpperCase()) != -1);
        },

        toggleCreateTarget() {

            this.isCreatingTarget = ! this.isCreatingTarget;
        },

        async targetPerformance() {

            this.startLoading();

            await axios.get(`/ucnull/targets/data/${this.currentTarget.id}`).then((res)=> {

                this.alertSuccess(res.data);

                this.targetMetrics = res.data;

                this.stopLoading();

            }).catch((e)=> {

                this.alertError(e.response.data);
                
                this.stopLoading();
            })
        },

        viewTarget(e) {

            var index = e.currentTarget.getAttribute('data-index');

            this.currentTarget = this.targetSearchList[index]

            this.view = 'single';
        },

        viewList() {
            this.currentTarget = {}
            this.targetMetrics = {}
            this.view = 'list'
        },

        limitText (count) {
            return `and ${count} other affiliates`
        },
        asyncFind (query) {

            this.isLoading = true

            axios.get(`/ucnull/affiliates/active`).then((res)=> {

                this.affiliates = res.data;
                this.isLoading = false

            }).catch((e)=>{

                this.alertError(e.response.data);
            })
        },

        clearAll () {
            this.newTarget.affiliates = []
        },

        async getAffiliates() {

            await axios.get(`/ucnull/affiliates/active`).then((res)=> {
                this.affiliates = res.data;

            }).catch((e)=>{

                this.alertError(e.response.data);
            })
        }
    }
}
</script>

<style scoped>
    .max-h-595 {
        max-height : 595px;
        overflow-y: scroll;
    }
</style>