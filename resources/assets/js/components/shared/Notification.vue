<template>

    <div class="alert alert-dismissible" :class="{'alert-success':!notification.read_at, 'alert-default': notification.read_at}" role="alert">
        <h4 class="alert-heading">{{notification.data.title}}</h4>
        
        <p class="mb-0">
            <a v-if="notification.data.link" :href="notification.data.link+'?notification='+notification.id">{{notification.data.desc}}</a>
            <span v-else>{{notification.data.desc}}</span>
        </p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click.prevent="dismiss">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>

<script>
export default {
    name : 'notification',
    props : {
        notification : {
            type : Object,
            required : true
        }
    },

    methods : {

        async dismiss(event) {


            await axios.post(`/notifications/read/${this.notification.id}`).then((res)=> {

                event.target.display = 'none';

            })
        }
    }
}
</script>

<style>
    .alert-default {

        background-color: #d7d8d8;
        border-color : #d7d8d8;
    }
</style>