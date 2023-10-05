const moment = require('moment');
const toastr = require('toastr');

const TOASTR_OPTIONS = {
    "closeButton": true,
    "debug": false,
    "positionClass": "toast-top-right",
    "toastClass": "black",
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

const REPORT_COLOR_CODES = {

    "001": "bg-success px-4 py-2 rounded",
    "002": "bg-primary px-4 py-2 rounded",
    "003": "bg-secondary px-4 py-2 rounded",
    "004": "bg-warning px-4 py-2 rounded",
    "005": "bg-danger px-4 py-2 rounded",
    "006": "bg-light px-4 py-2 rounded",
    "007": "bg-warning px-4 py-2 rounded",
    "009": "bg-warning px-4 py-2 rounded",
    "010": "bg-success px-4 py-2 rounded",
    "011": "bg-success px-4 py-2 rounded",
};
const REPORT_CODE_NAMES = {
    "001": "Loan Amount Disbursed",
    "002": "Collections",
    "003": "Fees Earned",
    "004": "Commission Paid",
    "005": "Active Loans",
    "006": "Penalty",
    "007": "Insurance Paid",
    "008": "Investments Made",
    "009": "Investors Statistics",
    "010": "Confirmed Payments",
    "011": "User Portfolio"
};
const SLIDER_OPTIONS = {
    eventType: 'auto',
    width: 'auto',
    height: 6,
    dotSize: 16,
    dotHeight: null,
    dotWidth: null,
    min: 0,
    max: 100,
    interval: 1,
    show: true,
    speed: 0.5,
    disabled: false,
    piecewise: false,
    piecewiseStyle: false,
    piecewiseLabel: false,
    tooltip: false,
    tooltipDir: 'top',
    reverse: false,
    data: null,
    clickable: true,
    realTime: false,
    lazy: false,
    formatter: null,
    bgStyle: null,
    sliderStyle: null,
    processStyle: null,
    piecewiseActiveStyle: null,
    piecewiseStyle: null,
    tooltipStyle: null,
    labelStyle: null,
    labelActiveStyle: null
};

export const utilitiesMixin = {
    data() {
        return {
            errorMessage: '',
            errorBag: [],
            loading: false,
            spinClass: {
                fa: true,
                "fa-check-circle-o": true,
                "fa-spin": false,
                "fa-spinner": false
            },
            sliderOptions: SLIDER_OPTIONS,
        };
    },

    methods: {
        startLoading() {
            console.log('starting loading')
            this.errorMessage = '';
            this.errorBag.splice(0, this.errorBag.length);
            this.loading = true;
            this.spinClass['fa-spinner'] = true;
            this.spinClass['fa-spin'] = true;
            this.spinClass['fa-check-circle-o'] = false;
        },

        stopLoading() {
            console.log('stopping loading')
            this.loading = false;
            this.spinClass['fa-spinner'] = false;
            this.spinClass['fa-spin'] = false;
            this.spinClass['fa-check-circle-o'] = true;
        },


        timeDifference(dateString) {
            const momentForm = moment(dateString, 'YYYY-MM-DD hh:mm:ss');
            return momentForm.fromNow();
        },

        formatAsCurrency(value) {
            return value.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },

        generateRandom(min, max) {
            return Math.random() * (max - min) + min;
        },

        alertInfo(message) {
            setTimeout(() => {
                toastr.info(message, "Info", TOASTR_OPTIONS);
            }, 1000);
        },

        alertSuccess(message) {
            setTimeout(() => {
                toastr.success(message, "Success", TOASTR_OPTIONS);
            }, 1000);
        },

        alertError(message) {
            setTimeout(() => {
                toastr.error(message, "Error", TOASTR_OPTIONS);
            }, 1000);
        },

        getEMI(rate = 0, nper = 0, pv = 0, fv = 0, type = 0) {
            if (rate > 0) {
                return (-fv - pv * Math.pow(1 + rate, nper)) / (1 + rate * type) / ((Math.pow(1 + rate, nper) - 1) / rate);
            } else {
                return (-pv - fv) / nper;
            }
        },

        getPrincipal(amount, tenure) {
            return this.round((amount / tenure), 2);
        },

        getInterest(rate, amount, tenure) {
            let a = (rate * tenure) * (12 / tenure);
            let interest = a / 12 * amount;
            return this.round(interest, 2);
        },

        getFlatEmi(rate, amount, tenure) {
            return this.getInterest(rate, amount, tenure) + this.getPrincipal(amount, tenure);
        },
        pmt(amount, rate, tenure) {

            var a = amount * rate;
            var b = 1 - Math.pow((1 + rate), -tenure);
            return a / b;
        },

        round(number, precision) {
            var shift = function(number, precision) {
                var numArray = ("" + number).split("e");
                return +(numArray[0] + "e" + (numArray[1] ? (+numArray[1] + precision) : precision));
            };

            return shift(Math.round(shift(number, +precision)), -precision);
        },

        handleApiErrors(e) {

            if (e.response && (e.response.status === 404 || e.response.status === 400 || e.response.status === 500)) {
                this.errorBag.push(e.response.data.message);
            } else if (e.response && e.response.status === 422) {

                const serverErrors = e.response.data.errors || {};
                Object.keys(serverErrors).forEach((key) => {
                    this.errorBag.push(serverErrors[key][0]);
                });
            } else {
                this.errorBag.push(e);
            }

            this.displayErrors();
        },

        displayErrors() {
            this.errorBag.forEach(e => {
                this.alertError(e);
            });
            this.errorBag = [];
        },

        getNameFromCode() {
            var code = this.code;
            return REPORT_CODE_NAMES[code];
        },

        getColorFromCode() {
            var code = this.code;
            return REPORT_COLOR_CODES[code];
        },

        validateEmail(email) {

            return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email) ? true : false;
        },

        Object2Array(obj) {

            return $.map(obj, function(value, index) {
                return [value];
            });
        },

        isEmpty(obj) {

            for (var key in obj) {

                if (obj.hasOwnProperty(key)) {

                    return false;
                }
            }

            return true;
        },



    }
};