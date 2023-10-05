<template>

<div>

    <div class="row">
          <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-between pb-2">
                  <h5>₦ {{ formatAsCurrency(wallet) }}</h5>
                  <i class="icon-graph"></i>
                </div>
                <div class="d-flex justify-content-between">
                  <p class="text-muted">Wallet</p>
                </div>
                <div class="progress progress-md">
                  <div
                    class="progress-bar bg-info w-100"
                    role="progressbar"
                    aria-valuenow="100"
                    aria-valuemin="0"
                    aria-valuemax="100"
                  ></div>
                </div>
              </div>
            </div>
          </div>
         

          <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
            <div class="card">
              <div class="card-body">

                <div v-if="transferring">
                    <div class="text-center">
                        <i class="fa fa-2x fa-spin fa-hourglass"></i><br>
                        <h6>Please wait.. </h6>
                    </div>
                </div>

                <div v-else>
                    <form method="POST"  @submit.prevent="initiateTransfer">

                        <div class="form-group" id="transfer-amount">
                            <label for="amount" class="form-control-label">Transfer Amount</label>
                            <input type="text" name="amount" id="amount" v-model="amount" class="form-control">
                        </div>

                        <div class="form-group" id="fund-direction">
                            <label for="fund-direction" class="form-control-label">Direction</label>
                            <select name="direction" v-model="direction" class="form-control">
                                <option value="">Choose a Direction</option>
                                <option value="VTW">Vault to Wallet</option>
                                <option value="WTV">Wallet to Vault</option>
                            </select>
                        </div>

                        <div class="form-group" id="submit-action">
                            <input type="submit" name="submit" class="btn btn-success" value="Transfer">
                        </div>
                    </form>
                </div>

              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-between pb-2">
                  <h5>₦ {{ formatAsCurrency(vault) }}</h5>
                  <i class="icon-pie-chart"></i>
                </div>
                <div class="d-flex justify-content-between">
                  <p class="text-muted">Vault</p>
                  <!--<p class="text-muted">max: 54</p>-->
                </div>
                <div class="progress progress-md">
                  <div
                    class="progress-bar bg-primary w-100"
                    role="progressbar"
                    aria-valuenow="100"
                    aria-valuemin="0"
                    aria-valuemax="100"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>

         <v-tour name="investorFundTransferTour" :steps="steps"></v-tour>
</div>
</template>
<script>
import {utilitiesMixin } from './../../mixins';
export default {

    name : 'wallet-vault-transfer',
    mixins : [utilitiesMixin],
    props : ['investor'],
    data(){

        return {
            wallet : this.investor.wallet,
            vault : this.investor.vault,
            amount : 0,
            direction : '',
            transferring : false,
             steps: [
                {
                  target: '#transfer-amount',  // We're using document.querySelector() under the hood
                
                  content: `You enter an amount you want to transfer here`
                },
                {
                  target: '#fund-direction',
                  content: 'Choose a direction to transfer the money from'
                },
                {
                  target: '#submit-action',
                  content: 'Finally hit the submit button and watch your funds move',
                  params: {
                    placement: 'top' // Any valid Popper.js placement. See https://popper.js.org/popper-documentation.html#Popper.placements
                  }
                }
              ]
        };
    },
    mounted(){
      this.$tours["investorFundTransferTour"].start();
    },
    methods : {

        async initiateTransfer(){
            if(! this.validate()) {
              return false;
            }
            this.transferring = true;
            let request = {amount: this.amount,flow : this.direction};
            await axios.post('/investor/transfer/funds/wallet-vault/'+this.investor.id,request).then((res)=>{
                this.wallet = res.data.wallet;
                this.vault = res.data.vault;
                this.transferring = false;
                this.alertSuccess('Transfer Successful');
            }).catch((e)=>{
                this.transferring = false;
                this.alertError(e);
            })
        },

        formatAsCurrency(value) {
            return value.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },

        validate(){
          if(this.direction.length < 1) {
            alert('Choose a proper direction for transfer');
            return false;
          }

          if(this.amount < 100){
            alert('Choose a proper amount for transfer');
            return false;
          }

          return true;
        }
        
    },

    watch : {

    }
}
</script>