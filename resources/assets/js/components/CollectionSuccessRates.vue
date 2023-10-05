<template>
  <div>

    <div class="">
      <form @submit.prevent="fillData">
        <div class="form-row">
          <div class="col-2">
            <select class="form-control" v-model="pickedMonth">
              <option v-for="(month, index) in months" :key="index" :value="index+1"> {{month}}</option>
            </select>
          </div>
          <div class="col-2">
            <select class="form-control" v-model="pickedYear">
              <option v-for="(year, index) in years" :key="index" :value="year"> {{year}}</option>
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

      <br><br>
    </div>
    <div class="row">

      <template v-if="!loading">
         <div class="offset-md- col-md-4" ><pie :chartdata="datacollection.data" :options="datacollection.options" v-if="datacollection" ></pie></div>
         <div class="offset-md-1 col-md-4" ><pie :chartdata="overallcollection.data" :options="overallcollection.options" v-if="overallcollection" ></pie></div>
      </template>

      <template v-else>
          <div class="offset-md-3 col-md-6">
            <chart-loader></chart-loader>
          </div>
      </template>
     
    </div>
  </div>
</template>

<script>
  import Doughnut from './charts/Doughnut'
  import Pie from './charts/Pie'
  import chartLoader from './shared/chartLoader';
  import { utilitiesMixin }  from '../mixins/index';

  const URLS = { fetchData : `/ucnull/collection/insights/success/rate`, fetchEmployers : `/api/employers/primary/json-data`};

  const colorCodeMapper = ['#008080',  '#D2691E',  '#BC8F8F',  '#4169E1'];

  export default {

    mixins: [utilitiesMixin],

    components: {
      Pie,
      'chart-loader': chartLoader
    },
    data () {
      return {
        datacollection: null,
        overallcollection: null,
        months : [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
        ],
        years : [2018, 2019, 2020, 2021, 2022, 2023, 2024 ],
        pickedEmployer : 1,
        pickedMonth : 1,
        pickedYear : 1,
        employers : [],
        successResults: []
      }
    },

    mounted () {
      this.getEmployers();
      let today = new Date();
      this.pickedMonth = today.getMonth() - 1;
      this.pickedYear = today.getFullYear();
      this.pickedEmployer = false;
      this.fillData();
      
    },

    methods: {
      async fillData () {

        this.startLoading();

        const paramsArray = { month : this.pickedMonth, year : this.pickedYear, employerID: this.pickedEmployer };

        await axios.get(URLS.fetchData, { params : paramsArray}).then((res)=> {
           this.buildCollectionFromResponse(res.data);
        }).catch((e)=> { console.log(e)})

        this.stopLoading();
      },

      buildCollectionFromResponse(collection){
          
          let collectionPerformance = {
            datasets: [{ data: Object.values(collection.performance), backgroundColor: colorCodeMapper}],
            labels : Object.keys(collection.performance)
          }
       
          let options  = { title : {display : true, text : 'Total collections'}}

          this.datacollection = { data : collectionPerformance, options : options}

          let randomBackgounds = ['#A4262C', '#0078D4'];
          let collectionOverall = {
            datasets : [{ data : Object.values(collection.overall), backgroundColor: randomBackgounds }],
            labels : Object.keys(collection.overall)
          }

          let overallOptions  = { title : {display : true, text : 'Overall Collection'}}

          this.overallcollection = { data : collectionOverall, options : overallOptions}
        
          
      },



      generateRandomColor() {
        return colorCodeMapper[Math.floor(Math.random() * colorCodeMapper.length)]
      },

      calculatePercentile(dataObject){
        let recoveries = dataObject.Recoveries;
        let pending = dataObject.Pending;

        let total = recoveries + pending;

        let percentile = (recoveries/total) * 100;

        return Math.round(percentile, 2);
      },

      async getEmployers () {
          await axios.get(URLS.fetchEmployers).then((res)=> {
            this.employers = res.data
          })
      }
    }
  }
</script>
