<template>
    <span>
        <button class="btn btn-primary btn-xs mt-3 mb-4" @click="showModal = true">Employment Data</button>
        
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>Employment Data
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div v-if="employment">
                    <h4>Employer: <strong>{{employment.employer.name}}</strong></h4>
                    <hr/>
                    <h4>Position: <strong>{{employment.position}}</strong></h4>
                    <hr/>
                    <h4>Department: <strong>{{employment.department}}</strong></h4>
                    <hr/>
                    <h4>State: <strong>{{employment.employer.state}}</strong></h4>
                </div>
                <div v-else>
                    <h4 class="text-center">Employment Data Unavailable</h4>
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
        props: ['employment'],
        
        data() {
            return {
                showModal: false,
            };
        },
    };
</script>