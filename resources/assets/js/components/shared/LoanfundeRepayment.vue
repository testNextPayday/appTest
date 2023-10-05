<template>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Reference</th>
				<th>Amount</th>
				<th>Duration</th>
				<th>Date Created</th>
			</tr>
		</thead>

		<tbody>
			<tr v-if="fundrepayments.length > 0" v-for="(repayments, keyObj) in fundrepayments ">
				<td>{{keyObj + 1}}</td>
				<td>{{fundrepayment.reference}}</td>
				<td>₦ {{repayments.amount.toLocaleString('en', {maximumFractionDigits: 2})}}</td>
				<td>{{fundrepayment.loan_request.duration}}</td>
				<td>{{repayments.created_at}}</td>
			</tr>

			<tr v-if="fundrepayments.length < 1">
                <td class="py-1 text-center" colspan="6">
                    There are no repayment yet
                </td>
            </tr>
		</tbody>
	</table>
</template>
<script>
	export default {
		props: ['fundrepayment'],

		data(){
			return{
				fundrepayments: []
			};
		},
		created(){
			this.fundrepayments = this.fundrepayment.repayment;
		}
	};
</script>