<template>
    <div class="card support-pane-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-0">Messages with {{ entityName }}</h4>
            </div>
            <div class="table-responsive support-pane no-wrap" style="height: 50vh; overflow-y:scroll;">
                <div class="t-row" v-for="message in messages" :key="message.id">
                    <div class="tumb">
                        <img class="img-sm rounded-circle" :src="message.sender.avatar || '/storage/defaults/avatars/default.png'" alt="Avatar">
                    </div>
                    <div class="content">
                        <p class="font-weight-bold mb-2 d-inline-block">
                            <span class="badge badge-danger" v-if="message.sender_type === 'App\\Models\\Admin'">ADMIN</span>
                            <span class="badge badge-primary" v-else-if="(message.sender_id === user.id) && (message.sender_type === userClass)">YOU</span>
                            <span class="badge badge-info" v-else>{{ message.sender.name }}</span>
                        </p>
                        <p class="text-muted mb-2 d-inline-block">{{ timeDifference(message.created_at) }}</p>
                        <p>{{ message.message }}</p>
                    </div>
          
                </div>
            </div>
            <div class="mt-5">
                <div class="row justify-content-center">
                    <div class="col-sm-6">
                        <form method="post" @submit.prevent="sendMessage" autocomplete="off">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" v-model="message" name="message" class="form-control"
                                        placeholder="Start Typing..." aria-label="message"
                                        required>
                                    <div class="input-group-append bg-info border-info">
                                        <button class="input-group-text bg-transparent text-white" :disabled="loading">
                                            <i :class="spinClass"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>  
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import { utilitiesMixin } from './../../mixins/';
    export default {
        props: ['existingMessages', 'entityName', 'sendRoute', 'user', 'userClass', 'code', 'base'],
        
        mixins: [utilitiesMixin],
        
        data() {
            return {
                messages: [],
                message: ''
            };
        },
        
        created() {
            this.openConversationChannel();  
        },
        
        mounted() {
            this.messages = this.existingMessages;
        },
        
        methods: {
            async sendMessage() {
                
                try {
                    this.startLoading();
                    
                    const response = await axios.post(this.sendRoute, { message: this.message});
                    
                    this.messages.unshift(response.data.message);
                    
                    this.message = '';
                    
                    this.alertSuccess('Message sent successfully');
                    
                    this.stopLoading();
                } catch (e) {
                    
                    this.handleApiErrors(e);
                    this.stopLoading();
                }
            },
            
            openConversationChannel() {
                makeEchoInstance(this.base).private(`conversations.${this.code}.${this.user.id}`)
                    .listen('MessageSent', (e) => {
                        this.messages.unshift(e.message);
                    });
            }
        }
    }
</script>
<style scoped>
    
</style>