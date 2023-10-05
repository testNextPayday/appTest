<template>
    <span>
        <button class="btn btn-info btn-xs" @click="showModal = true">View</button>
        
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>Loan Information
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div v-if="loan">
                    <h4>Reference: <strong>{{loan.reference}}</strong></h4>
                    <hr/>
                    <h4>EMI: <strong>₦{{loan.emi}}</strong></h4>
                    <hr/>
                    <h4>Duration: <strong>{{loan.duration}} Months</strong></h4>
                    <hr/>
                    <h4>Collected: <strong><small>{{loan.created_at}}</small></strong></h4>
                </div>
                <div v-else>
                    <h4 class="text-center">Loan Information Unavailable</h4>
                </div>
            </div>

            <div slot="footer">
                <div>
                    <button class="btn btn-dark pull-right" @click="showModal = false">
                        Close
                    </button>
                </div>
            </div>
        </modal>
    </span>
</template>
<script>
    export default {
        props: ['loan'],
        
        data() {
            return {
                showModal: false,
            };
        },
    };
</script>