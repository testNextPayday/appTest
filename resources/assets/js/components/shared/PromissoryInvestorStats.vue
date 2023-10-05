<template>

  <div>

    <div v-if="!fetchingData">

      <div class="row">
        <div class="col-12 card-statistics">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-2">
                                <h5>₦ {{ formatAsCurrency(investor.wallet) }}</h5>
                                <i class="icon-wallet"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <!--<p class="text-muted">Avg. Session</p>-->
                                <p class="text-muted">Wallet Balance</p>
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-info w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-2">
                                <h5>₦ {{ formatAsCurrency(investor.vault) }}</h5>
                                <i class="icon-wallet"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <!--<p class="text-muted">Avg. Session</p>-->
                                <p class="text-muted">Vault Balance</p>
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-info w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-2">
                                <h5>₦ {{ formatAsCurrency(investor.portfolioSize) }}</h5>
                              
                                <i class="icon-briefcase"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-muted">Portfolio Size</p>
                                <!--<p class="text-muted">max: 143</p>-->
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

                 <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-2">
                                ₦ {{ formatAsCurrency(investor.taxPaid) }}
                                <i class="icon-share"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <!--<p class="text-muted">Avg. Session</p>-->
                                <p class="text-muted">Tax Paid</p>
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-danger w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <div class="row">
          <div class="col-12 card-statistics">
              <div class="row">
                <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-2">
                                <h5>₦ {{ formatAsCurrency(investor.withdrawals) }}</h5>
                              
                                <i class="icon-briefcase"></i>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-muted">Withdrawals Made</p>
                                <!--<p class="text-muted">max: 143</p>-->
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                  <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                      <div class="card">
                        
                          <div class="card-body">
                              <div class="d-flex justify-content-between pb-2">
                                  ₦ {{ formatAsCurrency(investor.incomeEarned) }}
                                  <i class="icon-graph"></i>
                              </div>
                              <div class="d-flex justify-content-between">
                                  <!--<p class="text-muted">Avg. Session</p>-->
                                  <p class="text-muted">Income Earned</p>
                              </div>
                              <div class="progress progress-md">
                                  <div class="progress-bar bg-info w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                          </div>
                      </div>
                  </div>
                  
                  <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                      <div class="card">
                          <div class="card-body">
                              <div class="d-flex justify-content-between pb-2">
                                  ₦ {{ formatAsCurrency(investor.outstandingInterest) }}
                                  <i class="icon-hourglass"></i>
                              </div>
                              <div class="d-flex justify-content-between">
                                  <p class="text-muted">Outstanding Interest</p>
                                  <!--<p class="text-muted">max: 143</p>-->
                              </div>
                              <div class="progress progress-md">
                                  <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                      <div class="card">
                          <div class="card-body">
                              <div class="d-flex justify-content-between pb-2">
                                  ₦ {{ formatAsCurrency(investor.outstandingPrincipal) }}
                                  <i class="icon-diamond"></i>
                              </div>
                              <div class="d-flex justify-content-between">
                                  <p class="text-muted">Outstanding Principal</p>
                                  <!--<p class="text-muted">max: 120</p>-->
                              </div>
                              <div class="progress progress-md">
                                  <div class="progress-bar bg-success w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                          </div>
                      </div>
                  </div>

                  
              </div>
          </div>
      </div>

      <div class="row">
          <div class="col-12 card-statistics">
              <div class="row">
                  
              </div>
          </div>
      </div>

    </div>

    <div class="row" v-else>
      <div class="col-12">
        <div>
          <i class="fa fa-spinner fa-spin"></i> Please wait fetching Data
        </div>
      </div>
    </div>
<!-- 
    <v-tour name="investorDashboardTour" :steps="steps"></v-tour> -->
  
  </div>
</template>
<script>
import { utilitiesMixin } from "../../mixins";
export default {
  props: ["id"],
  mixins: [utilitiesMixin],
  name: "promissory-investor-statistics",
  data() {
    return {
      investor: [],
      fetchingData: false,
      steps: [
          {
            target: '#investor-wallet',  // We're using document.querySelector() under the hood
           
            content: `You money for funding is now kept in your <strong>Funding Wallet</strong>!`
          },
          {
            target: '#investor-vault',
            content: 'Introducing your protective new vault for recieving repayments<br> All your repayments are now bullet proved here <b>Vault</b>'
          },
          {
            target: '#investor-principal',
            content: 'All your active principals locked in loan funds you will see here',
            params: {
              placement: 'top' // Any valid Popper.js placement. See https://popper.js.org/popper-documentation.html#Popper.placements
            }
          }
        ]
    };
  },
  mounted() {
    this.getData();
   
  },
  methods: {
    getData() {
      this.fetchingData = true;
      axios
        .get("/api/get/investor/statistic/" + this.id)
        .then(res => {
          this.investor = res.data;
           //this.$tours['investorDashboardTour'].start();
        })
        .catch(e => {
          this.alertError(e);
        })
        .then(() => {
          this.fetchingData = false;
        });
    }
  }
};
</script>

