<template>
    <div style="display:inline">
         <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#managecategory">
            <i class="fa fa-plus"></i>
            Manage Bill Category
        </button>

        <div class="modal fade" id="managecategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-md" role="document">

                    <div class="modal-content">

                        <div class="modal-header">

                            <h4 class="modal-title">Manage Category</h4><br>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>

                        </div>

                        <div class="modal-body">

                            <div class="modal-upper-content">
                                <h4>Add a new Category</h4>
                                <form method="post" @submit.prevent="createNewBillCategory">
                                    <div class="form-group mb-3">

                                        <label for="bill-name" class="form-control-label">Name</label>
                                        <input type="text" name="bill-category-name" id="bill-category-name" v-model="newBillCategory.name" class="form-control" required>
                                    </div>
                                
                                    <button type="submit" class="btn btn-success" >Save</button>
                                </form>
                            </div>

                            <div class="modal-middle-content">
                                <h4>Choose a category activity</h4>
                                <button class="btn btn-sm btn-primary" :disabled="mode == 'edit'" @click="setMode('edit')">Edit Mode</button>
                                <button class="btn btn-sm btn-danger" :disabled="mode == 'delete'" @click="setMode('delete')">Delete Mode</button>
                            </div>

                            <div class="modal-lower-content">
                                <span v-if="mode == 'edit'" class="badge badge-primary"> Edit Mode</span>
                                <span v-if="mode == 'delete'" class="badge badge-danger"> Delete Mode</span>
                                <table>
                                    <thead>
                                        <tr>
                                            <th v-if="mode == 'delete'">Check</th>
                                            <th>Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(category, index) in categories" :key="index">
                                            <td v-if="mode == 'delete'">
                                                <input type="checkbox" :checked="categoryDelArr.includes(category.id)" @click="setupCategoryArr(category.id)">
                                            </td>
                                            <td>
                                                <span v-if="mode == 'delete'">{{category.name}}</span>
                                                <span v-else>
                                                    <input type="text" class="form-control" :ref="category.id" :value="category.name">
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <button class="btn btn-sm btn-primary" @click.prevent="completeAction">Complete Action</button>
                                    </tfoot>
                                </table>
                            </div>

                           

                        </div>

                    </div>
                    <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            newBillCategory: {
                name : ''
            },
            mode: 'delete',
            categoryDelArr : []
        };
    },

    computed : {
        categories() {
            return this.$parent.$store.state.billcategories;
        },

        categoryIds() {
            return this.categories.map((cat)=> cat.id);
        }
        
    },

    methods: {
        async createNewBillCategory(){

            $('#newbillcategory .close').click();

            this.$parent.loading = true;

            var req = this.newBillCategory;

            await axios.post(`/ucnull/bills/category/store`, req).then((res)=> {

                this.$parent.alertSuccess('New Bill Category Created');

                //refresh bills
                this.$parent.RefreshBillCategories();


            }).catch((e)=> {

                this.$parent.alertError(e.response.data);
            })

             $('.modal-backdrop').remove();

             this.$parent.loading = false;
        },

        
        setupCategoryArr(id){
            let index = this.categoryDelArr.indexOf(id);
            if (index == -1) {
                this.categoryDelArr.push(id);
            }else {
                this.categoryDelArr.splice(index, 1);
            }
        },
        setMode(value){
            this.mode = value;
        },

        getCategoryUpdates(){
            let updateObj = {};
            this.categoryIds.forEach(id => {
                let categoryName = this.$refs[id][0].value;
                updateObj[id] = categoryName;
            });
            return updateObj;
        },

        completeAction(){
            
           return this.mode == 'edit' ? this.postUpdates() : this.postDeletes();
        },

        async postUpdates() {
           
            $('#managebillcategory .close').click();

            this.$parent.loading = true;

            let updates = { updates: this.getCategoryUpdates()};

            await axios.post(`/ucnull/bills/category/updates`, updates).then((res)=> {

                this.$parent.alertSuccess('All updates posted');

                //refresh bills
                this.$parent.RefreshBillCategories();


            }).catch((e)=> {

                this.$parent.alertError(e.response.data);
            })

             $('.modal-backdrop').remove();

             this.$parent.loading = false;
        },

        async postDeletes() {
            $('#managebillcategory .close').click();

            this.$parent.loading = true;

            let ids = this.categoryDelArr;

            const deletes = {ids: ids};

            await axios.post(`/ucnull/bills/category/delete`, deletes).then((res)=> {

                this.$parent.alertSuccess('All deletes posted');

                //refresh bills
                this.$parent.RefreshBillCategories();


            }).catch((e)=> {

                this.$parent.alertError(e.response.data);
            })

             $('.modal-backdrop').remove();

             this.$parent.loading = false;
        }
    }
}
</script>

<style>
    .modal-upper-content, .modal-middle-content, .modal-lower-content {
        padding: 10px;
        margin: 3px;
    }
</style>