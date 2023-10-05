<template>
    <div class="p-4">
       
        <div class="row">
            <p>Select A Receiver : </p>
        </div>
        <div class="row">

            <div class="form-group col-md-4 radio has-input ">
                <input type="radio" value="" v-model="receiverType" name="receiverType">
                <label class="form-control-label">No One</label>
            </div>
            <div class="form-group col-md-4 radio has-input ">
                <input type="radio" value="affiliate" v-model="receiverType" name="receiverType">
                <label class="form-control-label">An Affiliate</label>
            </div>

            <div class="form-group col-md-4 radio has-input">
                 <input type="radio" value="staff" v-model="receiverType" name="receiverType">
                <label class="form-control-label">A Staff</label>
            </div>

            <div class="form-group col-md-4 radio has-input">
                 <input type="radio" value="investor" v-model="receiverType" name="receiverType">
                <label class="form-control-label">An Investor</label>
               
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-8" v-if="showAffiliates">
                <label class="form-control-label">Select an Affiliate </label>
                <select class="form-control" v-model="assignedPersonId" name="assignedPersonId">
                    <option v-for="(affiliate, index) in affiliates" :key="index" :value="affiliate.id">{{affiliate.name}}</option>
                </select>
            </div>

            <div class="form-group col-md-8" v-if="showStaffs">
                <label class="form-control-label">Select a Staff </label>
                <select class="form-control" v-model="assignedPersonId" name="assignedPersonId">
                    <option v-for="(staff, index) in staffs" :key="index" :value="staff.id">{{staff.firstname}} {{staff.lastname}} {{staff.midname}}</option>
                </select>
            </div>

            <div class="form-group col-md-8" v-if="showInvestors">
                <label class="form-control-label">Select an Investor</label>
                <select class="form-control" v-model="assignedPersonId" name="assignedPersonId">
                    <option v-for="(investor, index) in investors" :key="index" :value="investor.id">{{investor.name}}</option>
                </select>
            </div>

            
        </div>
           
    </div>
</template>

<script>
export default {
    props: {

        affiliates : {
            type : Array,
            required: true
        },

         staffs : {
            type : Array,
            required: true
        },

         investors : {
            type : Array,
            required: true
        },
    },
    data() {
        return {
            receiverType : "",
            showStaffs : false,
            showAffiliates : false,
            showInvestors : false,
            assignedPersonId : Math.floor(Math.random() * 10) +1 // random user between 1 and 10

        };
    },

    watch : {
        receiverType(value) {

            if(value == 'affiliate') {
                this.showStaffs = false;
                this.showInvestors = false;
                this.showAffiliates = true;
            }

            if(value == 'investor') {
                this.showStaffs = false;
                this.showInvestors = true;
                this.showAffiliates = false;
                
            }

            if (value == '') {
                this.showStaffs = false;
                this.showInvestors = false;
                this.showAffiliates = false;
            }

            if(value == 'staff') {
                this.showStaffs = true;
                this.showInvestors = false;
                this.showAffiliates = false;
               
            }
        }
    }
}
</script>


<style scoped>
    .form-group.has-input .form-control-label  {
        margin-bottom : 0px !important;
    }
</style>