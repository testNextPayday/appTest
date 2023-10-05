<template>
  <div class="row">

    <div class="col-md-10">
        <form @submit.prevent="fillData">
        <div class="form-row">
         
          <div class="col-2">
            <select class="form-control" v-model="pickedInterval">
              <option v-for="(interval, index) in intervals" :key="index" :value="interval.value"> {{interval.label}}</option>
            </select>
          </div>
          <div class="col-6">
             <select class="form-control" v-model="pickedEmployer">
               <option value="false">All Primary Employers</option>
              <option v-for="(employer, index) in employers" :key="index" :value="employer.id"> {{employer.name}}</option>
            </select>
          </div>

          <div class="col-2">
              <button class="btn btn-primary"> <i :class="spinClass"></i> View</button>
          </div>


        </div>
      </form>
    </div>
  <br><br>
    <div class="offset-md-2 col-md-8" v-if="!loading">
         <line-chart :chartdata="datacollection" :options="options" v-if="datacollection"></line-chart>
    </div>

    <template v-else>
        <div class="offset-md-3 col-md-6">
          <chart-loader></chart-loader>
        </div>
    </template>

   
  
  </div>
</template>

<script>
  import { utilitiesMixin }  from '../mixins/index';
  import Line from './charts/Line';

  import chartLoader from './shared/chartLoader';

  const URLS = { fetchData : `/ucnull/collection/insights/collection/intervals`, fetchEmployers : `/api/employers/primary/json-data`};
  export default {
    mixins: [utilitiesMixin],
    components: {
      'line-chart' : Line,
      'chart-loader': chartLoader
    },
    data () {
      return {
        datacollection: null,
        employers : [],
        pickedEmployer : false,
        pickedInterval : 3,
        intervals : [
          {label : 'Last 3 Months', value : 3}, 
          {label : 'Last 6 Months', value : 6}, 
          {label : 'Last 9 Months', value : 9}, 
          {label : 'Last 12 Months', value : 12} 
        ],
        options : {}
      }
    },
    mounted () {
       this.getEmployers();
      this.fillData()
    },
    methods: {
      async fillData () {

        this.startLoading();
       
        let paramsArray = { interval : this.pickedInterval, employerID: this.pickedEmployer };
        
        await axios.get(URLS.fetchData, { params : paramsArray}).then((res)=> {
          console.log(res.data);
           this.buildCollectionFromResponse(res.data);
        }).catch((e)=> { console.log(e)})

        this.stopLoading();

      },
       buildCollectionFromResponse(collection){
          
          let expectedCollections = {
                label : 'Expected Collections',
                fill : false,
                data : Object.values(collection['Expected Collections']),
                backgroundColor : '#9C2106',
                borderColor : '#06859C'
            };

          let actualCollections = {
                label : 'Actual Collections',
                fill : false,
                data : Object.values(collection['Actual Collections']),
                backgroundColor : '#D2691E',
                borderColor : '#D2691E'
          };

          this.datacollection = {
              labels : Object.keys(collection['Expected Collections']),
              datasets : [expectedCollections, actualCollections]
          }

          this.options = { 
            title : {display : true, text : 'Collection Perfomance Showing '+ this.pickedInterval+' Months'},
            responsive : true,
            tooltips: { mode : 'index', intersect : true},
            maintainAspectRatio: false,
          }
      },
      getRandomInt () {
        return Math.floor(Math.random() * (50 - 5 + 1)) + 5
      },

      async getEmployers () {
          await axios.get(URLS.fetchEmployers).then((res)=> {
            this.employers = res.data
          })
      }
    }
  }
</script>
