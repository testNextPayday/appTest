<template>
     <div>
        <div class="form-group">
            <label for="setting_id">Pick Preconfigured Settings</label>

            <select class="form-control" name="setting_id" id="setting_id" required v-model="settingsID"
             @change="setCurrentSettings">
                <option value="0">No Settings Available</option>
                <option v-for="(setting, index) in settings" :key="index" :value="setting.id" :data-index="index">{{setting.name}}</option>
            </select>
        </div>
        <template v-if="settingsID > 0 ">
            <div class="form-group">
                <label for="setting_id">Tax</label>
                <input type="text" class="form-control" name="tax" id="tax" v-model="currentSettings.tax_rate" required>
            </div>
            <div class="form-group">
                <label for="setting_id">Interest Rate</label>
                <input type="text" class="form-control" name="rate" id="rate" v-model="currentSettings.interest_rate" required>
            </div>
        </template>
     </div>
</template>
<script>
export 

default {
    
    props : ['settings'],

    data() {
        return {
            settingsID : 0,
            currentSettings : {}
        };
    },

    methods : {
        setCurrentSettings(e) {

            this.currentSettings = this.settings.find(item => item.id == e.target.value);
        }
    }

}
</script>