<template>
    <div class="col-md-4 p-3">
        <div class="card p-3">
            <div class="float-right"><img src="/images/birthday.png" width="30" height="30"></div>
            <div>{{person.name}}</div>
            <div>{{person.dob}}</div>
            <div>{{person.phone}}</div>
            <div>{{person.type}}</div>
        </div>
    </div>
</template>

<script>
export default {
    props : {

        person : {
            type :Object,
            required : true
        }
    }
}
</script>