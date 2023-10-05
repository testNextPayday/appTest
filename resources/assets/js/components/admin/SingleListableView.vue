<template>

    <div>

        <div v-if="loading">
            <newton-loader></newton-loader>
        </div>

        <div v-else>
           
            <div class="row" v-if="items.length > 0">

                <div class="col-md-10" v-if="screenView == 'list'">

                    <div class="card">

                        <div class="card-header">

                            <slot name="header"></slot>
                            
                            <div class="col-sm-3" style="display:inline-block">
                                <form >
                                    <input class="form-control" v-model="search" @keyup="filter" placeholder="Enter Bill" value="" required/>
                                </form>
                            </div>

                            <div class="pull-right">
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#newItem">
                                    <i class="fa fa-plus"></i>
                                    Add new
                                </button>
                            </div>

                        </div>

                        <div class="card-body">

                            <slot name="ListView"></slot>

                        </div>
                    </div>

                </div>
                <!--/.col-->

                <div class="col-md-12" v-if="screenView == 'single'">

                    <div class="row">

                        <div class="col-md-6 card">

                            <div class="card-header">

                                <h4 @click="viewList" style="cursor:pointer"><i class="fa  fa-arrow-left"> </i> Back</h4>

                            </div>

                            <div class="card-body">

                               <slot name="singleView"></slot>
                               
                            </div>

                        </div>

                        <div class="col-md-6">

                            <slot name="item-transactions"></slot>

                        </div>

                    </div>

                </div>

            </div>

            <div class="row" v-else>
                
                <div class="col-md-12" style="height:450px">

                    <div class="text-center" style="margin-top:150px">

                         <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#newItem">
                            <i class="fa fa-plus"></i>
                            Add new
                        </button>

                        <p style="font-size:120%;">You have not created any item yet</p>
                    </div>
                    

                </div>

            </div>


            <div class="modal fade" id="newItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

                <div class="modal-dialog modal-sm" role="document">

                        <slot name="add-modal-content"></slot>
                        
                        <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <div class="modal fade" id="updateitem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

                <div class="modal-dialog modal-sm" role="document">

                        <slot name="update-modal-content"></slot>
                        <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

        </div>

       
    </div>
</template>
<script>

import { utilitiesMixin } from './../../mixins';

export default {

    mixins: [utilitiesMixin],

    props : {
        items : Array,
        requied : true
    },

    data(){

        return {

            screenView : 'list',

            loading : false,

            search : '',

            currentItem : {}

        }
    },

    computed : {

    },


    methods : {

        filter(){

            return this.items = this.items.filter((item)=>item.name.indexOf(this.search));
        },

      

        viewItem(e) {
          
            var index = e.currentTarget.getAttribute('data-index');

            this.currentItem  = this.items[index];

            this.$emit('currentItem',this.currentItem)

            this.screenView  = 'single';

        },

        viewList()
        {
            this.currentItem = '';

            this.screenView = 'list';
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

</style>