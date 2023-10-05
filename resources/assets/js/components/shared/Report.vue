<template>
  <div class="row">
    <report-item
      :code="'001'"
      :employers="employers"
      :amount="amountDisbursed"
      :count="amountDisbursedCount"
      ></report-item>

    <report-item :code="'002'"  :amount="repayments" :count="repaymentCount"></report-item>
    <report-item :code="'003'"  :employers="employers" :amount="feesEarned" :count="feesEarnedCount"></report-item>
    <report-item
      :code="'004'"
      :amount="commissionGiven"
      :count="commissionGivenCount"
      :affiliates="affiliates"
      
    ></report-item>
    <report-item
      :code="'005'"
      :employers="employers"
      :amount="activeLoans"
      :count="activeLoansCount"
      
    ></report-item>
    <report-item :code="'006'" :amount="penalties" :count="penaltiesCount"  ></report-item>
    <report-item :code="'007'" :amount="insurances" :count="insuranceCount"  :employers="employers"></report-item>
    <report-item :code="'008'" :amount="investments" :count="investmentCount" :investors="investors"></report-item>
    <report-item :code="'009'" :amount="investments" :count="investmentCount" :investors="investors"></report-item>
    <report-item :code="'010'" :amount="[]" :count="investmentCount" :employers="employers"></report-item>
    <report-item
      :code="'011'"
      :employers="employers"
      :amount="userPortfolios"
      :count="userPortfolioCount"      
    ></report-item>
    
  </div>
</template>
<script>
import reportItem from "./ReportItem";

export default {
  components: {
    reportItem
  },
  data() {
    return {
      employers: [],
      affiliates: [],
      investors: [],
      amountDisbursed: "",
      amountDisbursedCount: "",
      repayments:'',
      repaymentCount:'',
      collectionsMade: "",
      collectionsMadeCount: "",
      feesEarned: "",
      feesEarnedCount: "",
      commissionGiven: "",
      commissionGivenCount: "",
      activeLoans: "",
      activeLoansCount: "",      
      penalties: "",
      penaltiesCount: "",
      insurances : '',
      insuranceCount : '',
      investments : '',
      investmentCount : '',
      portfolioFees: '',
      portfolioFeesCount : '',
      userPortfolios: "",
      userPortfolioCount: ""
    };
  },
  async mounted() {
    this.employers = await this.getEmployers();
    this.affiliates = await this.getAffiliates();
    this.investors = await this.getInvestors();
    // [this.amountDisbursedCount, this.amountDisbursed] =  this.getStatistic(
    //   "loans-disbursed"
    // );

    //  [this.repaymentCount, this.repayments] =  this.getStatistic(
    //   "repayments"
    // );

    // [this.collectionsMadeCount, this.collectionsMade] =  this.getStatistic(
    //   "collections-made"
    // );
    // [this.feesEarnedCount, this.feesEarned] =  this.getStatistic(
    //   "fees-earned"
    // );
    // [this.commissionGivenCount, this.commissionGiven] =  this.getStatistic(
    //   "commissions-given"
    // );
    // [this.activeLoansCount, this.activeLoans] =  this.getStatistic(
    //   "active-loans"
    // );
    // [this.penaltiesCount, this.penalties] =  this.getStatistic(
    //   "penalties"
    // );
    // [this.insuranceCount, this.insurances] =  this.getStatistic(
    //   "insurances"
    // );
    // [this.investmentCount, this.investments] =  this.getStatistic(
    //   "investments"
    // );
    // [this.userPortfolioCount, this.userPortfolios] =  this.getStatistic(
    //   "user-portfolios"
    // );
   
    
  },
  methods: {
    getEmployers() {
      var employers = axios
        .get("/ucnull/reports/primary/employers")
        .then(function(response) {
          return response.data;
        });
      return employers;
    },

    getAffiliates() {
      var affiliates = axios
        .get("/ucnull/reports/affiliates")
        .then(function(response) {
          return response.data;
        });
      return affiliates;
    },
     getInvestors() {
      var investors = axios
        .get("/ucnull/reports/investors")
        .then(function(response) {
          return response.data;
        });
      return investors;
    },

    initDataTable() {
     alert('opening modal')
      $("#order-listing").DataTable({
        aLengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf"],
        iDisplayLength: 5,
        language: {
          search: ""
        }
      });
    },
    getStatistic(name) {
      var stat = axios.get("/ucnull/reports/" + name).then(function(response) {
        return response.data;
      });

      return stat;
    }
  }
};
</script>

<style scoped>

 .modal-mask .modal-wrapper .modal-container[data-v-2c928174] .card {
  width: 1000px;
}


.modal-body .card-body {
  padding: 15px 30px;
}
</style>