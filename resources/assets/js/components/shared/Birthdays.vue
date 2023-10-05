<template>

    <div>

        <h4 class="card-header">Birthdays Today</h4>
        <div class="row">
             <birthdayCard :person="person" v-for="(person, index) in birthdaysToday" :key="index"></birthdayCard>
        </div>

        <br><br>

        <h4 class="card-header">Birthdays Tomorrow</h4>
         <div class="row">
             <birthdayCard :person="person" v-for="(person, index) in birthdaysTomorrow" :key="index"></birthdayCard>
        </div>

        <div>
            <h4 class="card-header">Find Upcoming Birthdays</h4>
            <form class="row p-3" @submit.prevent="searchBirthdays">
                <div class="col-md-3">
                    <label>Day</label>
                    <select class="form-control" v-model="searchParams.day">
                        <option value="">Any</option>
                        <option v-for="(day, index) in daysInMonth()" :key="index" :value="day">{{day}}</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Month</label>
                    <select class="form-control" v-model="searchParams.month">
                        <option value="">Any</option>
                        <option v-for="(month, index) in monthsInYear" :key="index" :value="month.number">{{month.name}}</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Users</label>
                    <select class="form-control" v-model="searchParams.type">
                        <option value="">All</option>
                        <option value="borrower">Borrowers</option>
                        <option value="investor">Investor</option>
                        <option value="staff">Staff</option>
                        <option value="affiliate">Affiliate</option>
                    </select>
                </div>

               <div class="form-group col-md-3">
                    <label class="form-control-label"></label><br/>
                    <button type="submit" class="btn btn-primary mt-1" > <i :class="spinClass"></i> Search</button>
                </div> 
                
            </form>
            <br>

            


            <div v-if="searchedBirthdays.length > 0">
                <div class="row">
                     <birthdayCard :person="person" v-for="(person, index) in searchedBirthdays" :key="index"></birthdayCard>
                </div>
            </div>
            <div v-else>
                <p>No Birthdays found</p>
            </div>
        </div>

    </div>

</template>

<script>

import BirthdayCard from './BirthdayCard';

import {utilitiesMixin} from './../../mixins';

export default {

    mixins: [utilitiesMixin],

    components : {
        birthdayCard : BirthdayCard
    },
    data() {
        return{

            birthdaysToday : [],
            birthdaysTomorrow : [],
            searchedBirthdays : [],
            searchParams : {
                month : '',
                day : '',
                type : '',
            },
            daysInMonth : function() {

                var holder = [];

                for(var i = 1; i <= 100; i++) {
                    holder.push(i);
                }

                return holder;
            },

            monthsInYear : [
                {name : 'January' , number : 1},
                {name : 'Febuary' , number : 2},
                {name : 'March' , number : 3},
                {name : 'April' , number : 4},
                {name : 'May' , number : 5},
                {name : 'June' , number : 6},
                {name : 'July' , number : 7},
                {name : 'August' , number : 8},
                {name : 'September' , number : 9},
                {name : 'October' , number : 10},
                {name : 'November' , number : 11},
                {name : 'December' , number : 12},
            ]

        };
    },

    mounted() {

        this.getBirthdaysToday();

        this.getBirthdaysTomorrow();
    },

    methods : {

        async getBirthdaysToday() {

            await axios.get('/ucnull/birthdays/today').then((res)=> {
                this.birthdaysToday = res.data;
            }); 
        },

        async getBirthdaysTomorrow() {

            await axios.get('/ucnull/birthdays/tomorrow').then((res)=> {
                this.birthdaysTomorrow = res.data;
            }); 
        },

        async searchBirthdays() {

            this.startLoading();

            this.searchedBirthdays = [];

            var day = this.searchParams.day;
            var month = this.searchParams.month;
            var type = this.searchParams.type;

            const query = {day, month, type};

            await axios.get('/ucnull/birthdays/search', {params : query}).then((res)=> {

                this.searchedBirthdays = res.data
            });

            this.stopLoading();
        }
    }
    
}
</script>