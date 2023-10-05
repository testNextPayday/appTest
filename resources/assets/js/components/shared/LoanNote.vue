<template>
    <div class="card">

        <div class="card-header">
             <div v-if="editable" class="float-right">
                <form style="display:inline" :action="deleteurl" method="POST">
                     <input type="hidden" name="_token" :value="deletetoken">
                    <button class="btn btn-danger"><i class="fa fa-trash"></i> </button>
                </form>
                
                <button class="btn btn-primary" @click="editing = ! editing"><i class="fa fa-edit"></i></button>
            </div>
            <h4 class="card-title">{{owner}}</h4>
           
        </div>

        <div class="card-body">

            <div v-if="!editing">
                {{note}}
            </div>

            <div v-else>
                <form method="POST" :action="url">
                    <input type="hidden" name="_token" :value="token">
                    <div class="form-group">
                        <label class="form-control-label">Note</label>
                        <textarea name="note" v-model="noteDesc" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-success"> Save </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    name : 'loan-note',

    props : {
        url : {
            type : String
        },
        editable : {
            type : Number,
            default : 0
        },
        token : {
            type : String
        },
        note : {
            type : String
        },

        deleteurl : {
            type : String
        },

        deletetoken : {
            type : String
        },

        owner : {
            type : String
        }
    },

    data() {
        return {
          editing : false,
          noteDesc : this.note
        };
    }
}
</script>