<template>
    <p class="mb-3">
        Fund Status: &nbsp;
        <span v-if="status == 1" class="text-warning">Pending</span>
        <span v-else-if="status == 2" class="text-info">Active</span>
        <span v-else-if="status == 3" class="text-secondary">Cancelled</span>
        <span v-else-if="status == 4" class="text-primary">On Transfer</span>
        <span v-else-if="status == 5" class="text-success">Transferred</span>
        <span v-else class="text-success">Fullfilled</span>
    </p>
</template>
<script>
    export default {
        props: ['status']
    }
</script>