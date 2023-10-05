<template>
    <span>
        <button class="btn btn-danger btn-xs" @click="showModal = true">View</button>
        
        <modal v-if="showModal" @close="showModal = false">
            <div slot="header">
                <h4>Borrower Data
                    <span class="justify-content-right">
                        <button type="button" class="close" @click="showModal = false">&times;</button>
                    </span>
                </h4>
            </div>

            <div slot="body">
                <div v-if="user" class="text-center">
                    <h4 class="text-center">
                        <img :src="user.avatar" width="200px" class="img-thumbnail img-circle"/>
                    </h4>
                    
                    <h4>{{user.name}}</h4>
                    <h5>{{ user.email }}</h5>
                    <p><strong>{{ user.reference }}</strong></p>
                </div>
                <div v-else>
                    <h4 class="text-center">Borrower Data Unavailable</h4>
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
        props: ['user'],
        
        data() {
            return {
                showModal: false,
            };
        },
    };
</script>