<template>
    <div class="row">
        <div class="form-group col-sm-6">
            <label>Select Type</label>
            <select class="form-control" v-model="type" required>
                <option v-for="key in Object.keys(collectionPlans || {})" 
                    :value="key" :key="key">
                    {{ key.toUpperCase() }}
                </option>
            </select>
        </div>
        
        <div class="form-group col-sm-6">
            <label>Select Method</label>
            <select class="form-control" :name="fieldName" v-model="plan" required>
                <option v-for="key in Object.keys(selectedTypes)"
                    :key="key"
                    :value="key">
                    {{ selectedTypes[key] }}
                </option>
            </select>
        </div>
    </div>
</template>
<script>
    export default {
       
        props : {

            collectionPlans :{
                type : Object,
                required : true
            },

            fieldName : {
                type : String,
                required : true
            },

            selected : {

                type : [Number,String],
                required : false
            },
            
            isSecondary : {

                type : Boolean,
                default: false
            }

        },
        data() {
            return {
                type: '',
                plan: '',
            };
        },
        
        mounted() {
            // Copy over if there's an existing selection
            if (this.selected) {
                this.plan = this.selected;
                this.type = 
                    Object.keys(this.collectionPlans)
                    [Number(`${this.selected}`.substring(0,1)) - 1];
            }
                
        },
        
        watch: {

           plan : function(val){

              switch(this.isSecondary){

                  case true :

                     this.$emit("secondaryPlanChanged",val)

                  break;

                  default :

                    this.$emit("primaryPlanChanged",val)
              }
               
           }
        },

        computed : {
            selectedTypes : function(){
                const options = this.collectionPlans[this.type];
                
                const selectedTypes = {};
                
                if (options) {
                    Object.keys(options).forEach(key => {
                        selectedTypes[key] = options[key];
                    })
                }
               
                return selectedTypes;
            }
        }
    };
</script>