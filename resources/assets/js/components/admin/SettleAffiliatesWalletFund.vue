<template>
    <form method="POST" :action="url" class="card p-5">
        <input type="hidden" name="_token" :value="token">

         <div class="row">
            <div class="form-group col-md-4 radio ">
                <input type="radio" value="affiliate" v-model="receiverType" name="receiverType">
                <label class="form-control-label">Choose an Affiliate</label>
            </div>

            <div class="form-group col-md-4 radio">
                 <input type="radio" value="staff" v-model="receiverType" name="receiverType">
                <label class="form-control-label">Choose a Staff</label>
            </div>

            <div class="form-group col-md-4 radio">
                 <input type="radio" value="investor" v-model="receiverType" name="receiverType">
                <label class="form-control-label">Choose an Investor</label>
               
            </div>
        </div>

		<div class="row" v-if="loaded">

			<div class="col">
				<label for="from">Select Investor</label>
				<select type="text" class="form-control" name="investorId"   id="from" data-live-search="true" v-model="selectedInvestor">
					<option v-for="(investor,index) in investors" :value="investor.id" :key="index"  :data-index="index" > {{investor.name }} </option>
				</select>
			</div>

            <div class="col">
				<label for="from">Select Wallet Fund</label>
				<select type="text" class="form-control" name="fund_id"   id="from" data-live-search="true">
					<option v-for="(fund, index) in funds" :value="fund.id" :key="index">({{fund.reference}}) {{fund.amount }} </option>
				</select>
			</div>


            <div class="col">

                <div class="row">

                    <div class="form-group col-md-8" v-if="showAffiliates">
                        <label class="form-control-label">Select an Affiliate </label>
                        <select class="form-control" v-model="assignedPersonId" name="assignedPersonId" data-live-search="true">
                            <option v-for="(affiliate, index) in affiliates" :key="index" :value="affiliate.id">{{affiliate.name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-8" v-if="showStaffs">
                        <label class="form-control-label">Select a Staff </label>
                        <select class="form-control" v-model="assignedPersonId" name="assignedPersonId" data-live-search="true">
                            <option v-for="(staff, index) in staffs" :key="index" :value="staff.id">{{staff.firstname}} {{staff.lastname}} {{staff.midname}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-8" v-if="showInvestors">
                        <label class="form-control-label">Select an Investor</label>
                        <select class="form-control" v-model="assignedPersonId" name="assignedPersonId" data-live-search="true">
                            <option v-for="(investor, index) in investors" :key="index" :value="investor.id">{{investor.name}}</option>
                        </select>
                    </div> 

                </div>

            </div>

			
		</div>
        <newton-loader v-else></newton-loader>
        

		<div class="mt-3">
			<button class="btn btn-info float-right" >Settle Affiliate</button>
		</div>
	</form>

</template>
<script>
import { utilitiesMixin } from './../../mixins';
export default {
    name : 'settle-affiliates-wallet-fund',
     mixins: [utilitiesMixin],

    props : {
        
        url : {
            type : String,
            required : true
        },
        token : {
            type : String,
            required :true
        }
    },
    data() {
        return {
            investors : [],
            affiliates : [],
            staffs : [],
            receiverType : "affiliate",
            showStaffs : false,
            showAffiliates : true,
            showInvestors : false,
            assignedPersonId : Math.floor(Math.random() * 10) +1, // random user between 1 and 10
            loaded : false,
            currentInvestor : {},
            selectedInvestor : Math.floor(Math.random() * 10) +1
        };
    },

    mounted() {

        this.getAllNeededData();
        
    },

    computed:{

        funds:  {
            set: function(newValue){
                this.currentInvestor.funds = newValue;
            },
            get : function() {
                return this.currentInvestor.funds;
            }
        }
    },

    watch : {

        selectedInvestor(value) {

            this.currentInvestor = this.investors.find((investor)=> investor.id == value);
            this.funds = this.currentInvestor.funds;

        },

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

            if(value == 'staff') {
                this.showStaffs = true;
                this.showInvestors = false;
                this.showAffiliates = false;
               
            }
        }
    },

    methods : {

        setCurrentInvestor(e){
            
            var investorId = e.target.value;
            
        },

        async getAllNeededData() {
            await axios.get(`/ucnull/wallet-fund/commission/data`).then((res)=> {
                let data = res.data;
                this.affiliates = data['affiliates'];
                this.staffs = data['staffs'];
                this.investors = data['investors'];
                this.loaded  = true
            })
        }
    }
}
</script>