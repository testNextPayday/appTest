<template>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><i class="text-facebook icon-user"></i></th>
                <th>Investor</th>
                <th>Bid Rating</th>
                <th>Amount</th>
                <th>When</th>
                <th>Manage</th>
            </tr>
        </thead>
  
        <tbody>
            <tr v-if="bids.length > 0" v-for="bid in bids">
                <td class="py-1">
                    <img :src="bid.investor.avatar" alt="image" />
                </td>
                <td>{{ bid.investor.name }}</td>
                <td>
                    <div class="progress">
                      <div class="progress-bar bg-success" role="progressbar"
                        :style="'width: ' + bidPercent(bid.amount) + '%'"
                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </td>
                <td>
                    ₦ {{ formatAsCurrency(bid.amount) }}
                </td>
                <td>{{ timeDifference(bid.created_at) }}</td>
                <td>
                    <div class="btnFlex">
                        <template v-if="bid.status == 1">
                            <button class="btn btn-info btn-xs" @click="acceptBid(bid.id)"
                                :disabled="loading">Accept</button>
                            <button class="btn btn-danger btn-xs" @click="rejectBid(bid.id)"
                                :disabled="loading">Reject</button>    
                        </template>
                        <button v-else-if="bid.status == 2" class="btn btn-success btn-block btn-xs">Accepted</button>
                        <button v-else-if="bid.status == 3" class="btn btn-danger btn-block btn-xs">Rejected</button>
                        <button v-else class="btn btn-secondary btn-block btn-xs">Cancelled</button>
                    </div>
                </td>
            </tr>
            <tr v-if="bids.length < 1">
                <td class="py-1 text-center" colspan="6">
                    There are no bids for this asset yet
                </td>
            </tr>
        </tbody>
    </table>
</template>
<script>
import { utilitiesMixin } from './../../mixins';

export default {
    props: ['fund', 'bids'],
    
    mixins: [utilitiesMixin],
    
    methods: {
        bidPercent(amount) {
            return amount / this.fund.sale_amount * 100;
        },

        async acceptBid(id) {
            let proceed = confirm('Are you sure?');
            
            if (!proceed) return;
            
            this.startLoading();
            try {
                const response = await axios.post(`/bids/${id}/accept`);
                
                if (response.data.status === 1) {
                    this.alertSuccess(response.data.message);
                    const bid = this.bids.find(bid => bid.id === id);
                    bid.status = 2;
                    
                    const otherBids = this.bids.filter(bid => bid.id !== id && bid.status == 1);
                    otherBids.forEach(bid => bid.status = 4);
                } else {
                    this.alertError(response.data.message);
                }
                
                this.stopLoading();
            } catch(e) {
                this.handleApiErrors(e);
                this.stopLoading(); 
            };
        },
        
        async rejectBid(id) {
            let proceed = confirm('Are you sure?');
            
            if (!proceed) return;
            
            this.startLoading();
            try {
                const response = await axios.post(`/bids/${id}/reject`);
                
                if (response.data.status === 1) {
                    this.alertSuccess(response.data.message);
                    const bid = this.bids.find(bid => bid.id === id);
                    bid.status = 3;
                } else {
                    this.alertError(response.data.message);
                }
                
                this.stopLoading();
            } catch(e) {
                this.handleApiErrors(e);
                this.stopLoading(); 
            };
            
        },
    }
    
};
</script>