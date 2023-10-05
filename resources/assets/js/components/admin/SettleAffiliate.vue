<template>
    <form method="POST" :action="url" class="card p-5" >
        <input type="hidden" name="_token" :value="token">
		<div class="row">
			<div class="col">
				
				<label for="from">Select Loan Request</label>
				<select type="text" class="form-control" name="loanRequestId"   id="from" data-live-search="true">
					<option v-for="(loanRequest,index) in loanrequests" :value="loanRequest.id" :key="index">{{loanRequest.user.name }} ₦({{formatAsCurrency(loanRequest.amount)}})</option>
				</select>
			</div>

			<div class="col">
				
				<label for="to">Select Receiving Affiliate</label>
				<select type="text" class="form-control" name="affiliateId" id="to" data-live-search="true">
					<option v-for="(affiliate,index) in affiliates" :value="affiliate.id" :key="index" >{{affiliate.name}}</option>
				</select>
			</div>
		</div>

		<div class="mt-3">
			<button class="btn btn-info float-right" >Settle Affiliate</button>
		</div>
	</form>

</template>
<script>
import { utilitiesMixin } from './../../mixins';
export default {
    name : 'settle-affiliates',
     mixins: [utilitiesMixin],

    props : {
        loanrequests : {
            type : Array,
            required : true,

        },
        affiliates : {
            type  :Array,
            required : true
        },
        url : {
            type : String,
            required : true
        },
        token : {
            type : String,
            required :true
        }
    }
}
</script>