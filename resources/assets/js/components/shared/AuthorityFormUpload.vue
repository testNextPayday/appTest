<template>
    
    <div>
        <div v-if="success">
            <p class="text-success">Upload was successful</p>
        </div>
        <form v-else class="row" method="post" enctype="multipart/form-data" @submit.prevent="UploadForm($event)">                       
            <div class="form-group col-sm-4">
                <label>Upload Signed Authority Form</label>
                <input type="file" name="authority_form" class="form-control" required/>
                <br/>
                <button class="btn btn-primary btn-sm"><i :class="spinClass"></i> Upload</button>
            </div>

            <span v-if="error">{{error}}</span>
        </form>
    </div>
</template>

<script>
import {utilitiesMixin} from './../../mixins';
export default {

    mixins : [utilitiesMixin],
    props : {
        loan :{
            type:Object,
            required : true
        }
    },

    data() {

        return {
            error : '',
            success : ''
        };
    },

    methods : {

        async UploadForm(event) {

            console.log(event.target[0].files)
            this.startLoading();

            var url = `/loans/mandates/authority-form/${this.loan.reference}`;

            var formData = new FormData();

            formData.append('authority_form', event.target[0].files[0])

            await axios.post(url, formData).then((res)=> {

                this.success = res.data

            }).catch((e)=> {
                this.error = e.response.data;
            })

            this.stopLoading();
        }
    }
}
</script>