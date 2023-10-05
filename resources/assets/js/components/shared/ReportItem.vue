<template>
  <div class="col-md-6 grid-margin">
    <div class="card">

      <div class="card-body">

          <h4 :class="['card-header',getColorFromCode()]">View {{getNameFromCode()}} <span style="width:10px;"></span><i class="fa fa-bar-chart"></i></h4>
           <small class="text-gray">
            <a class="text-gray" href="#" @click="openModal()" >View Reports</a>
          </small>
          
      </div>

     

    </div>

    <modal v-if="showModal" @close="closeModal" id="modal">
      <div slot="header">
        <h4>
          Reports On {{getNameFromCode()}}
          <span class="justify-content-right">
            <button type="button" class="close" @click="showModal = false">&times;</button>
          </span>
        </h4>
      </div>

      <div slot="body">
        <!-- Loan Disbursement starts here -->
        <div v-if="code == '001'">
          <div class="screen">
            <form action method="POST" class="row" id="loan_disbursed">
              <div class="form-group col-md-3 row">
                <label class="form-control-label">Start Date</label>
                <input
                  type="date"
                  name="startDate"
                  class="form-control"
                  v-model="disbursalReport.from"
                />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">End Date</label>
                <input type="date" name="endDate" class="form-control" v-model="disbursalReport.to" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">Loan Type</label>
                <select name="loan_type" class="form-control" v-model="disbursalReport.loantype">
                  <option value="All">All</option>
                  <option value="topups">Topups</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">Employer</label>
                <select name="employer" class="form-control" v-model="disbursalReport.employer">
                  <option value>All</option>
                  <option
                    v-for="employer in employers"
                    :value="employer.id"
                    :key="employer.id"
                  >{{employer.name}}</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label"></label>
                <br />
                <button
                  type="submit"
                  name="submit"
                  class="btn btn-success"
                  @click.prevent="handleRequest(disbursalReport)"
                >
                  <i :class="spinClass"></i>
                  {{buttonText}}
                </button>
              </div>
            </form>

            <div class="display">{{disbursalReport.info}}</div>

            <div>
              <table class="table table-responsive" id="order-listing">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Loan Ref</th>
                    <th>Borrower</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>EMI</th>
                    <th>Tenure</th>
                    <th>MDA</th>
                    <th>Payroll ID</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(loan,index) in disbursalReport.data" :key="index">
                    <td>{{index + 1}}</td>
                    <td>
                     {{loan.reference}}
                    </td>
                    <td>{{loan.user.name}}</td>
                    <td>{{loan.created_at}}</td>
                    <td>₦ {{formatAsCurrency(loan.amount)}}</td>
                    <td>₦ {{formatAsCurrency(loan.emi)}}</td>
                    <td>{{loan.duration}}</td>
                    <td>{{loan.mda}}</td>
                    <td>{{loan.payroll_id}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <p></p>
        </div>
        <!-- Loan Disbursement ends here -->

         <!-- repayment starts here -->
        <div v-if="code == '002'">
          <div class="screen">
            <form action method="POST" class="row" id="repayments">
              <div class="form-group col-md-3 row">
                <label class="form-control-label">Start Date</label>
                <input
                  type="date"
                  name="startDate"
                  class="form-control"
                  v-model="repaymentReport.from"
                />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">End Date</label>
                <input type="date" name="endDate" class="form-control" v-model="repaymentReport.to" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">Filter</label>
                <select name="employer" class="form-control" v-model="repaymentReport.criteria">
                  <option value="">All</option>
                  <option value="1">Paid</option>
                  <option value="2">Unpaid</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label"></label>
                <br />
                <button
                  type="submit"
                  name="submit"
                  class="btn btn-success"
                  @click.prevent="handleRequest(repaymentReport)"
                >
                  <i :class="spinClass"></i>
                  {{buttonText}}
                </button>
              </div>
            </form>

            <div class="display">{{repaymentReport.info}}</div>

            <div>
              <table class="table table-responsive" id="order-listing">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Date</th>
                    <th>Due Amount</th>
                    <th>Borrower</th>
                    <th>Payroll ID</th>
                    <th>Employer</th>
                    <th>Loan</th>
                    <th>Staff</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(plan,index) in repaymentReport.data" :key="index">
                    <td>{{index + 1}}</td>
                   
                    <td>{{plan.payday}}</td>
                    <td>{{formatAsCurrency(plan.collection_amount)}}</td>
                    <td>{{plan.loan.user.name}}</td>
                    <td>{{plan.payroll_id}}</td>
                    <td>{{plan.employer}}</td>
                    <td>{{plan.loan.reference}}</td>
                    <td>{{plan.collector}}</td>
                    <td>
                      <span v-if="plan.paid_out" class="badge badge-success">Paid</span>
                      <span v-else class="badge badge-danger">Not Paid</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <p></p>
        </div>
        <!-- Repayment ends here -->

        <!-- Fees end starts -->
        <div v-if="code == '003'">
          <div class="screen">
            <form action method="POST" class="row" id="fees_earned">
              <div class="form-group col-md-3 row">
                <label class="form-control-label">Start Date</label>
                <input type="date" name="startDate" class="form-control" v-model="feesEarned.from" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">End Date</label>
                <input type="date" name="endDate" class="form-control" v-model="feesEarned.to" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">Employer</label>
                <select name="employer" class="form-control" v-model="feesEarned.employer">
                  <option value>All</option>
                  <option
                    v-for="employer in employers"
                    :value="employer.id"
                    :key="employer.id"
                  >{{employer.name}}</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label"></label>
                <br />
                <button
                  type="submit"
                  name="submit"
                  class="btn btn-success"
                  @click.prevent="handleRequest(feesEarned)"
                >
                  <i :class="spinClass"></i>
                  {{buttonText}}
                </button>
              </div>
            </form>

            <div class="display">{{feesEarned.info}}</div>

            <div>
              <table class="table table-responsive" id="order-listing">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Loan Ref</th>
                    <th>Date</th>
                    <th>Fee</th>
                    <th>Employer</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(fee,index) in feesEarned.data" :key="index">
                    <td>{{index + 1}}</td>
                    <td>
                      {{fee.loan.reference}}
                    </td>
                    <td>{{fee.created_at}}</td>
                    <td>{{fee.management_fee.toFixed(2)}}</td>
                    <td>{{fee.employer}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- Fees earned end -->

        <!-- Commissions given start -->
        <div v-if="code == '004'">
          <div class="screen">
            <form action method="POST" class="row" id="commissions_given">
              <div class="form-group col-md-3 row">
                <label class="form-control-label">Start Date</label>
                <input
                  type="date"
                  name="startDate"
                  class="form-control"
                  v-model="commissionGiven.from"
                />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">End Date</label>
                <input type="date" name="endDate" class="form-control" v-model="commissionGiven.to" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">Affiliate</label>
                <select name="affiliate" class="form-control" v-model="commissionGiven.affiliate">
                  <option value>All</option>
                  <option
                    v-for="affiliate in affiliates"
                    :value="affiliate.id"
                    :key="affiliate.id"
                  >{{affiliate.name}}</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label"></label>
                <br />
                <button
                  type="submit"
                  name="submit"
                  class="btn btn-success"
                  @click.prevent="handleRequest(commissionGiven)"
                >
                  <i :class="spinClass"></i>
                  {{buttonText}}
                </button>
              </div>
            </form>

            <div class="display">{{commissionGiven.info}}</div>

            <div>
              <table class="table table-responsive" id="order-listing">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Borrower</th>
                    <th>Affiliate</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Entity</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(commission,index) in commissionGiven.data" :key="index">
                    <td>{{index + 1}}</td>
                    <td>{{commission.borrower}}</td>
                    <td>{{commission.owner.name}}</td>
                    <td>{{commission.created_at}}</td>
                    <td>{{formatAsCurrency(commission.amount)}}</td>
                    <td>{{commission.entity.reference}}</td>
                    <td>{{commission.description}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- Commissions given ends -->

        <!-- Active loans start -->
        <div v-if="code == '005'">
          <div class="screen">
            <form action method="POST" class="row" id="active_loans">
              <div class="form-group col-md-3 row">
                <label class="form-control-label">Start Date</label>
                <input type="date" name="startDate" class="form-control" v-model="activeLoan.from" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">End Date</label>
                <input type="date" name="endDate" class="form-control" v-model="activeLoan.to" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">Employer</label>
                <select name="employer" class="form-control" v-model="activeLoan.employer">
                  <option value>All</option>
                  <option
                    v-for="employer in employers"
                    :value="employer.id"
                    :key="employer.id"
                  >{{employer.name}}</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label"></label>
                <br />
                <button
                  type="submit"
                  name="submit"
                  class="btn btn-success"
                  @click.prevent="handleRequest(activeLoan)"
                >
                  <i :class="spinClass"></i>
                  {{buttonText}}
                </button>
              </div>
            </form>

            <div class="display">{{activeLoan.info}}</div>
            <br />

            <div>
              <table class="table table-responsive" id="order-listing">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Customer ID</th>
                    <th>Borrower</th>
                    <th>BVN</th>
                    <th>DOB</th>
                    <th>Gender</th>
                    <th>Phone Number</th>
                    <th>Address</th>                    
                    <th>Payroll ID</th>
                    <th>EMI</th>
                    <th>Amount</th>
                    <th>Created Date</th>
                    <th>Due Date</th>
                    <th>Duration</th>
                    <th>Repay Made</th>
                    <th>Repay Left</th>
                    <th>MDA</th>
                    
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(loan,index) in activeLoan.data" :key="index">
                    <td>{{index + 1}}</td>
                    <td>{{loan.user.reference}}</td>
                    <td>{{loan.user.name}}</td>
                    <td>{{loan.user.bvn}}</td>
                    <td>{{loan.user.dob}}</td>
                    <td>{{loan.user.gender}}</td>
                    <td>{{loan.user.phone}}</td>
                    <td>{{loan.user.address}}</td>
                    <td>
                      <span v-if="loan.loan_request.employment">
                          {{loan.loan_request.employment.payroll_id}}
                      </span>

                      <span v-else> N/A </span>
                    </td>
                    <td>{{loan.emi.toFixed(2)}}</td>
                    <td>{{formatAsCurrency(loan.amount)}}</td>
                    <td>{{loan.created_at}}</td>
                    <td>{{loan.due_date}}</td>
                    <td>{{loan.duration}}</td>
                    <td>{{loan.repayments_made}}</td>
                    <td>{{loan.repayments_left}}</td>
                    <td>
                        <span v-if="loan.loan_request.employment">
                            {{loan.loan_request.employment.mda}}
                        </span>
                        <span v-else> N/A </span>
                    </td>
                    
                     
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- Active loans ends -->

         <!-- Insurance start -->
        <div v-if="code == '007'">
          <div class="screen">
            <form action method="POST" class="row" id="insurance_paid">
              <div class="form-group col-md-3 row">
                <label class="form-control-label">Start Date</label>
                <input type="date" name="startDate" class="form-control" v-model="insurancePaid.from" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">End Date</label>
                <input type="date" name="endDate" class="form-control" v-model="insurancePaid.to" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">Employer</label>
                <select name="employer" class="form-control" v-model="insurancePaid.employer">
                  <option value>All</option>
                  <option
                    v-for="employer in employers"
                    :value="employer.id"
                    :key="employer.id"
                  >{{employer.name}}</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label"></label>
                <br />
                <button
                  type="submit"
                  name="submit"
                  class="btn btn-success"
                  @click.prevent="handleRequest(insurancePaid)"
                >
                  <i :class="spinClass"></i>
                  {{buttonText}}
                </button>
              </div>
            </form>

            <div class="display">{{insurancePaid.info}}</div>
            <br />

            <div>
              <table class="table table-responsive" id="order-listing">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Insurance</th>
                    <th>Borrower</th>
                    <th>Created Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(loan,index) in insurancePaid.data" :key="index">
                    <td>{{index + 1}}</td>
                    <td>{{loan.insurance.toFixed(2)}}</td>
                    <td>{{loan.borrower}}</td>
                    <td>{{loan.created_at}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- Insurance ends -->

         <!-- Investment start -->
        <div v-if="code == '008'">
          <div class="screen">
            <form action method="POST" class="row" id="investments_made">
              <div class="form-group col-md-3 row">
                <label class="form-control-label">Start Date</label>
                <input type="date" name="startDate" class="form-control" v-model="investmentMade.from" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">End Date</label>
                <input type="date" name="endDate" class="form-control" v-model="investmentMade.to" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">Investor</label>
                <select name="employer" class="form-control" v-model="investmentMade.investor">
                  <option value>All</option>
                  <option
                    v-for="investor in investors"
                    :value="investor.id"
                    :key="investor.id"
                  >{{investor.name}}</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label"></label>
                <br />
                <button
                  type="submit"
                  name="submit"
                  class="btn btn-success"
                  @click.prevent="handleRequest(investmentMade)"
                >
                  <i :class="spinClass"></i>
                  {{buttonText}}
                </button>
              </div>
            </form>

            <div class="display">{{investmentMade.info}}</div>
            <br />

            <div>
              <table class="table table-responsive" id="order-listing">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Amount</th>
                    <th>Monthly Repayment</th>
                    <th>Reference</th>
                    <th>Loan </th>
                    <th>Investor</th>
                    <th>Percentage</th>
                    <th>Created Date</th>
                   
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(investment,index) in investmentMade.data" :key="index">
                    <td>{{index + 1}}</td>
                    <td>{{formatAsCurrency(investment.amount)}}</td>
                    <td>{{investment.monthly_pay}}</td>
                    <td>{{investment.reference}}</td>
                    <td>{{investment.loan.reference}}</td>
                    <td>{{investment.investor_name}}</td>
                    <td>{{investment.percentage}}</td>
                    <td>{{investment.created_at}}</td>
                    <!-- <td>{{investment.collections_made}}</td>
                    <td>{{investment.collections_left}}</td> -->
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- Investment ends -->

         <!-- Investment start -->
        <div v-if="code == '009'">
          <div class="screen">
            <form action method="POST" class="row" id="investors_stats">
              <div class="form-group col-md-2 row">
                <label class="form-control-label">Start Date</label>
                <input type="date" name="startDate" class="form-control" v-model="investorStat.from" />
              </div>

              <div class="form-group col-md-2">
                <label class="form-control-label">End Date</label>
                <input type="date" name="endDate" class="form-control" v-model="investorStat.to" />
              </div>

               <div class="form-group col-md-3">
                <label class="form-control-label">Activity</label>
                 <select name="activity" class="form-control" v-model="investorStat.activity">
                  <option value="000">Wallet Funds</option>
                  <option value="012">Recoveries</option>
                  <option value="002">Withdrawals</option>
                  <option value="003">Loan Funds</option>
                  <option value="025">Taxes</option>
                  <option value="027">Portfolio Charge</option>
                  <option value="016">Corrective RVSL</option>
                  <option value="013">Recovery Fees</option>
                  <option value="024">Loan Voids</option>
                  <option value="033">Affiliate Loan Cost </option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">Investor</label>
                <select name="employer" class="form-control" v-model="investorStat.investor">
                  <option value>All</option>
                  <option
                    v-for="investor in investors"
                    :value="investor.id"
                    :key="investor.id"
                  >{{investor.name}}</option>
                </select>
              </div>

              <div class="form-group col-md-2">
                <label class="form-control-label"></label>
                <br />
                <button
                  type="submit"
                  name="submit"
                  class="btn btn-success"
                  @click.prevent="handleRequest(investorStat)"
                >
                  <i :class="spinClass"></i>
                  {{buttonText}}
                </button>
              </div>
            </form>

            <div class="display">{{investorStat.info}}</div>
            <br />

            <div>
              <table class="table table-responsive" id="order-listing">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Direction</th>
                    <th>Owner</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(stat,index) in investorStat.data" :key="index">
                    <td>{{index + 1}}</td>
                    <td>{{formatAsCurrency(stat.amount)}}</td>
                    <td>{{stat.description}}</td>
                    <td>{{stat.direction == 1 ? 'INFLOW' : 'OUTFLOW'}}</td>
                    <th>{{stat.owner.name}}</th>
                    <td>{{stat.created_at}}</td>
                    <!-- <td>{{investment.collections_made}}</td>
                    <td>{{investment.collections_left}}</td> -->
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- Investment ends -->

         <!-- Investment start -->
        <div v-if="code == '010'">
          <div class="screen">
            <form action method="POST" class="row" id="investors_stats">
              <div class="form-group col-md-2 row">
                <label class="form-control-label">Start Date</label>
                <input type="date" name="startDate" class="form-control" v-model="confirmedPayments.from" />
              </div>

              <div class="form-group col-md-2">
                <label class="form-control-label">End Date</label>
                <input type="date" name="endDate" class="form-control" v-model="confirmedPayments.to" />
              </div>

            

              <!-- <div class="form-group col-md-3">
                <label class="form-control-label">Employer</label>
                <select name="employer" class="form-control" v-model="confirmedPayments.employers">
                  <option value>All</option>
                  <option
                    v-for="employer in employers"
                    :value="employer.id"
                    :key="employer.id"
                  >{{employer.name}}</option>
                </select>
              </div> -->

              <div class="form-group col-md-3">
                <label class="form-control-label"></label>
                <br />
                <button
                  type="submit"
                  name="submit"
                  class="btn btn-success"
                  @click.prevent="handleRequest(confirmedPayments)"
                >
                  <i :class="spinClass"></i>
                  {{buttonText}}
                </button>
              </div>
            </form>

            <div class="display">{{confirmedPayments.info}}</div>
            <br />

            <div>
              <table class="table table-responsive" id="order-listing">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Borrower</th>
                    <th>Payroll ID</th>
                    <th>Loan Amount</th>
                    <th>Repayment</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(stat,index) in confirmedPayments.data" :key="index">
                    <td>{{index + 1}}</td>
                    <td>{{stat.loan.user ? stat.loan.user.name : 'NILL'}}</td>
                    <td>{{stat.payroll_id ? stat.payroll_id: 'NILL'}}</td>
                    <td>{{stat.loan.amount}}</td>
                    <th>{{formatAsCurrency(stat.amount)}}</th>
                    <td>{{stat.updated_at}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- Investment ends -->

         <!-- User Portfolio start -->
        <div v-if="code == '011'">
          <div class="screen">
            <form action method="POST" class="row" id="user_portfolio">
              <div class="form-group col-md-3 row">
                <label class="form-control-label">Start Date</label>
                <input type="date" name="startDate" class="form-control" v-model="userPortfolio.from" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">End Date</label>
                <input type="date" name="endDate" class="form-control" v-model="userPortfolio.to" />
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">Employer</label>
                <select name="employer" class="form-control" v-model="userPortfolio.employer">
                  <option value>All</option>
                  <option
                    v-for="employer in employers"
                    :value="employer.id"
                    :key="employer.id"
                  >{{employer.name}}</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label class="form-control-label">Filter No. of Days</label>
                <select name="criteria" class="form-control" v-model="userPortfolio.criteria">
                  <option value="30">30</option>
                  <option value="60">60</option>
                  <option value="90">90</option>
                </select>
              </div>


              <div class="form-group col-md-3">
                <label class="form-control-label"></label>
                <br />
                <button
                  type="submit"
                  name="submit"
                  class="btn btn-success"
                  @click.prevent="handleRequest(userPortfolio)"
                >
                  <i :class="spinClass"></i>
                  {{buttonText}}
                </button>
              </div>
            </form>

            <div class="display">{{userPortfolio.info}}</div>
            <br />

            <div>
              <table class="table table-responsive" id="order-listing">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Customer ID</th>
                    <th>Borrower</th>  
                    <th>Loan Reference</th>      
                    <th>Phone Number</th>                                        
                    <th>Payroll ID</th>
                    <th>EMI</th>
                    <th>Wallet Balance</th>
                    <th>Created Date</th>
                    <th>Due Date</th>
                    <th>Duration</th>
                    <th>Repay Made</th>
                    <th>Repay Left</th>                                         
                    <th>Notify</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(plan,index) in userPortfolio.data" :key="index">
                    <td>{{index + 1}}</td>
                    <td>{{plan.loan.user.reference}}</td>
                    <td>{{plan.loan.user.name}}</td>
                    <td>{{plan.loan.reference}}</td>  
                    <td>{{plan.loan.user.phone}}</td>
                    <td>
                      <span v-if="plan.payroll_id">
                          {{plan.payroll_id}}
                      </span>

                      <span v-else> N/A </span>
                    </td>
                    <td>{{plan.emi.toFixed(2)}}</td>
                    <td>{{formatAsCurrency(plan.loan.user.wallet)}}</td>
                    <td>{{plan.updated_at}}</td>
                    <td>{{plan.payday}}</td>
                    <td>{{plan.loan.duration}}</td>
                    <td>{{plan.repayments_made}}</td>
                    <td>{{plan.repayments_left}}</td>                                    
                    <td><input type="checkbox" class="form-control" :checked="notifyArr.includes(plan.loan.reference)" @click="handleNotifyArr($event, (plan.loan.reference))" ></td>
                  </tr>
                </tbody>
              </table>
                <div class="card col-md-4">                  
                  <div class="form-group">
                      <button @click="sendNotification" class="btn btn-success" >Send Notification</button>
                  </div>                  
                </div>
            </div>
          </div>
        </div>
        <!-- User Portfolio ends -->


        
      </div>

      <div slot="footer"></div>
    </modal>

  </div>
</template>
<script>
import { utilitiesMixin } from "../../mixins";
export default {
  props: ["code", "employers", "amount", "count", "affiliates","investors"],
  mixins: [utilitiesMixin],
  data() {
    return {
      showModal: false,
      buttonText: "Submit",
      userReference: '',
      loanReference:'',
      notifyArr : [],
      disbursalReport: {
        name: 'disbursalReport',
        from: "",
        to: "",
        info: "",
        employer: "",
        loantype : 'All',
        code: this.code,
        data: [],
        showLoans: false
      },
      repaymentReport: {
        name: 'repaymentReport',
        from: "",
        to: "",
        info: "",
        employer: "",
        code: this.code,
        data: [],
        showLoans: false
      },
      feesEarned: {
        name : 'feesEarned',
        from: "",
        to: "",
        info: "",
        employer: "",
        code: this.code,
        data: [],
        showFees: false
      },
      commissionGiven: {
        name :'commissionGiven',
        from: "",
        to: "",
        info: "",
        affiliate: "",
        code: this.code,
        data: [],
        showCommissions: false
      },
      activeLoan: {
        name: 'activeLoan',
        from: "",
        to: "",
        info: "",
        employer: "",
        code: this.code,
        data: [],
       
      },
      userPortfolio: {
        name: 'userPortfolio',
        from: "",
        to: "",
        info: "",
        employer: "",
        criteria:"",
        code: this.code,
        data: [],
       
      },
       insurancePaid: {
        name: 'insurancePaid',
        from: "",
        to: "",
        info: "",
        employer: "",
        code: this.code,
        data: [],
       
      },
       investmentMade: {
        name: 'investmentMade',
        from: "",
        to: "",
        info: "",
        investor: "",
        code: this.code,
        data: [],
        showInvestments: false
      },
       investorStat: {
        name: 'investorStat',
        from: "",
        to: "",
        info: "",
        investor: "",
        activity:'',
        code: this.code,
        data: [],
      },
      confirmedPayments: {
        name: 'confirmedPayments',
        from: "",
        to: "",
        info: "",
        code: this.code,
        data: []
      },
     
    };
  },
  mounted() {},
  watch: {},
  beforeUpdate : function(){
     if ( $.fn.DataTable.isDataTable('#order-listing') ) {
             $('#order-listing').DataTable().destroy()
     }
  },
  updated() {
        this.initDataTable();
  },
  methods: {
    closeModal() {
      this.showModal = false;
    },
    openModal() {
      this.showModal = true;
    },

    async sendNotification(){  
      
        let param ={notifyArr : this.notifyArr}
      
      await axios.post('/ucnull/reports/send/repayment/notification', param).then(response => {
          if(response.data.status === 1) {
              this.alertSuccess(response.data.message);            
          } else {
              this.alertError(response.data.message);
          }    
          this.stopLoading();
      }).catch(error => {
          this.alertError(error);
          this.stopLoading();
      });
    },

    handleNotifyArr(e, reference){
      // if reference is in array we remove it from the array
      // else we add it to the array
      if(this.notifyArr.includes(reference)){
          let index = this.notifyArr.indexOf(reference);
          this.notifyArr.splice(index, 1);
      }else{
          this.notifyArr.push(reference)
      }
    },

    initDataTable() {
      
      $("#order-listing").DataTable({
        aLengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf"],
       
        iDisplayLength: 5,
        sPaginationType: "full_numbers",
        language: {
          search: ""
        }
      });
    },

    async handleRequest(requestObject) {
      var object = requestObject.name
      var $this = this;
      var data = $this[object]
      var code  = $this.code
      $this[object].info = "";
      $this[object].data = [];
      this.startLoading();
      await axios.post("/ucnull/reports", {data,code})
        .then(function(response) {
          $this[object].info = response.data.info;
          $this[object].data = response.data.result;
          $this.stopLoading();
        })
        .catch(function(e) {
          $this.handleApiErrors(e);
        })
       
    },


  }
};
</script>
<style scoped>


.w-1000px {
  width:1000px;
}
#modal >>> .modal-container {
   width:1000px;
}
.modal-body .card-body {
  padding: 15px 30px;
}
</style>
