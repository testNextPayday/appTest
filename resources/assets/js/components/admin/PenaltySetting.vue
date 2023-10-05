<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Penalty Settings</h3>
                </div>

                <div class="card-body">
                    <form method="POST" @submit.prevent="saveChanges">

                        <input type="hidden" v-model="entity_id">

                        <input type="hidden" v-model="entity_type">

                        <div class="form-group">
                            <label class="form-control-label">Fixed or Percent(%)</label>
                            <select class="form-control" v-model="type">
                                <option value="F">Fixed Amount</option>
                                <option value="P">Percent Based</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label class="form-control-label">
                                <span v-if="type== 'F'">Enter Fixed Amount</span>
                                <span v-if="type == 'P'"> Enter Percent (%)</span>
                            </label>
                            <input type="number" step="0.01"  class="form-control" v-model="value">
                        </div>

                        <div class="form-group">
                            <label class="form-control-label"> Grace Period
                            </label>
                            <input type="number" class="form-control" v-model="grace_period">
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">Penalty Status</label>
                            <select class="form-control" v-model="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">Maturity Penalty Status</label>
                            <select class="form-control" v-model="excess_penalty_status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-success float-right" type="submit"><i :class="spinClass"></i> Save</button>
                        </div>
                    </form>
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
        setting : { type : Object, required : true},
        entity_type : { type : String},
        entity_id : {type : Number},
    },
    data() {
        return {
            type : this.setting.type ?  this.setting.type : 'F',
            value : this.setting.value ? this.setting.value :  '',
            grace_period : this.setting.grace_period ? this.setting.grace_period : '',
            status : this.setting.status  != null ? this.setting.status :  1,
            excess_penalty_status : this.setting.excess_penalty_status  != null ? this.setting.excess_penalty_status :  1,
        }
    },

    methods : {

        async saveChanges() {
            this.startLoading();
            const req = { 
                type : this.type, 
                value : this.value, 
                grace_period : this.grace_period,
                entity_id : this.entity_id,
                entity_type : this.entity_type,
                setting_id : this.setting.id,
                status: this.status,
                excess_penalty_status: this.excess_penalty_status
            };
            await axios.post(`/ucnull/penalty-settings/create`, req).then((res)=> { this.alertSuccess(res.data)}).catch((e)=> { this.alertError(e.response)});

            this.stopLoading();
        }
    }
}
</script>