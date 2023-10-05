<template>
    <div class="container">
        <div class="large-12 medium-12 small-12 cell">
            
            <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
            <label for="file"><strong>Choose a file</strong></label>
            <br>
            <progress max="100" :value.prop="uploadPercentage"></progress>
            <br>
            <button v-on:click="submitFile()">Submit</button>
        </div>
        
        
        <div>
            <b-progress :value="counter" :max="max" show-progress animated></b-progress>
            <b-progress class="mt-1" :max="max" show-value>
              <b-progress-bar :value="counter*(6/10)" variant="success"></b-progress-bar>
              <b-progress-bar :value="counter*(2.5/10)" variant="warning"></b-progress-bar>
              <b-progress-bar :value="counter*(1.5/10)" variant="danger"></b-progress-bar>
            </b-progress>
            <b-btn class="mt-4" @click="clicked">Click me</b-btn>
          </div>
    </div>
</template>
<script>
    export default {
        data(){
            return {
                file: '',
                uploadPercentage: 0,
                counter: 45,
      max: 100
            };
        },  
        
        methods: {
            handleFileUpload(){
              this.file = this.$refs.file.files[0];
            },
            clicked () {
      this.counter = Math.random() * this.max
      console.log('Change progress to ' +
        Math.round(this.counter * 100) / 100)
    },
  
            /*
              Submits the file to the server
            */
            submitFile(){
                /*
                    Initialize the form data
                */
                let formData = new FormData();
            
                /*
                    Add the form data we need to submit
                */
                formData.append('file', this.file);
            
                /*
                    Make the request to the POST /single-file URL
                */
                axios.post( '/test-upload', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                  
                    onUploadProgress: function( progressEvent ) {
                        this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
                    }.bind(this)
                
                    
                }).then(function(){
                    console.log('SUCCESS!!');
                }).catch(function(){
                    console.log('FAILURE!!');
                });
            },
        }
    };
</script>
<style scoped>
    #file {
    	width: 0.1px;
    	height: 0.1px;
    	opacity: 0;
    	overflow: hidden;
    	position: absolute;
    	z-index: -1;
    }
    
    #file + label {
        font-size: 1.25em;
        font-weight: 700;
        color: white;
        background-color: #d3394c;
        text-overflow: elipsis;
        display: inline-block;
        cursor: pointer;
        padding: 1em 2em;
    }
    
    #file:focus + label,
    #file + label:hover {
        background-color: #4b0f31;
    }
    
    #file:focus + label {
    	outline: 1px dotted #000;
    	outline: -webkit-focus-ring-color auto 5px;
    }
    
    #file + label * {
	    pointer-events: none;
    }
</style>