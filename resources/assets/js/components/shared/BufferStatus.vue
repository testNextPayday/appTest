<template>
    <div>
        <span v-for="(buffer, index) in buffers" :key="index" style="margin:3px;" :title="buffer.status_message">
            <i :class="buffer.status ? 'fa fa-2x fa-check-square text-success' :'fa fa-2x fa-times-circle text-danger'"></i>    
        </span>
    </div>
</template>

<script>
export default {

    props : {
        buffers : {
            type : Array,
            required : true
        }
    },


    
}
</script>

<style scoped>

.fa-2x {
    font-size : 1.4em;
}
</style>