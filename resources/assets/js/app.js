/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window._ = require('lodash');

import Multiselect from 'vue-multiselect'




/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

// register globally
Vue.component('multiselect', Multiselect)
Vue.component('assign-investor-commission', require('./components/admin/AssignInvestorCommission'));
Vue.component('settle-affiliates-wallet-fund', require('./components/admin/SettleAffiliatesWalletFund'));
Vue.component('settle-affiliates-promissory-note', require('./components/admin/SettleAffiliatePromissoryNote'));
Vue.component('bills-manager', require('./components/admin/BillsManager'));
Vue.component('bills-statistics', require('./components/admin/BillStatistics'));
Vue.component('bill-requests', require('./components/admin/BillRequest'));
Vue.component('target-manager', require('./components/admin/TargetManager'));
Vue.component('settle-affiliates', require('./components/admin/SettleAffiliate'));
Vue.component('managed-loan-sweeper', require('./components/admin/ManagedLoanSweeper'));
Vue.component('transactions-manager', require('./components/admin/TransactionsManager'));
Vue.component('gateway-transaction', require('./components/admin/GatewayTransaction'));
Vue.component('transfer-controls', require('./components/admin/TransferControl'));
Vue.component('staff-salary', require('./components/admin/StaffSalary'));
Vue.component('refund-payments', require('./components/admin/RefundPayments'));
Vue.component('collection-insight', require('./components/admin/CollectionInsight'));

Vue.component('admin-edit-settings-on-note', require('./components/admin/AdminEditSettingsOnNote'));
Vue.component('penalty-settings', require('./components/admin/PenaltySetting'));
Vue.component('treat-penalty', require('./components/admin/TreatPenalty'));
Vue.component('transact-wallet', require('./components/admin/TransactWallet'));
Vue.component('paystack-sync', require('./components/admin/PaystackSync'));

Vue.component('init', require('./components/users/Init'));
Vue.component('user-referral', require('./components/users/UserReferral'));
Vue.component('password-reset-phone', require('./components/users/PasswordResetPhone'));
Vue.component('bulk-repayment', require('./components/BulkRepayment'));
Vue.component('wallet', require('./components/users/Wallet'));
Vue.component('escrow', require('./components/users/Escrow'));
Vue.component('fund-loan', require('./components/users/FundLoan'));
Vue.component('bid-component', require('./components/users/BidComponent'));
Vue.component('bid-update', require('./components/users/BidUpdate'));
Vue.component('loan-bid-component', require('./components/users/LoanBidComponent'));
Vue.component('phone-verification', require('./components/users/PhoneVerification'));
Vue.component('employment', require('./components/users/Employment'));
Vue.component('bank-details', require('./components/users/BankDetails'));
Vue.component('register-component', require('./components/users/RegisterComponent'));
Vue.component('key-officers', require('./components/users/KeyOfficers'));
Vue.component('loan-request-form-section', require('./components/users/LoanRequestFormSection'));
Vue.component('salary-now-loan-request-form-section', require('./components/users/SalaryNowLoanRequestFormSection'));

Vue.component('user-loan-setup-form', require('./components/users/UserLoanSetupForm'));
Vue.component('pay-installments', require('./components/users/PayInstallments'));

Vue.component('staff-fund-loan', require('./components/staff/FundLoan'));
Vue.component('staff-loan-bid-component', require('./components/staff/LoanBidComponent'));
Vue.component('staff-group-borrowers', require('./components/staff/GroupBorrowers'));

Vue.component('resolve-account', require('./components/shared/ResolveAccount'));
Vue.component('resolve-card', require('./components/shared/ResolveCard'));
Vue.component('search-borrowers', require('./components/shared/SearchBorrowers'));
Vue.component('buffer-status', require('./components/shared/BufferStatus'));
Vue.component('list-borrowers', require('./components/shared/ListBorrowers'));
Vue.component('penalty-schedule', require('./components/shared/PenaltySchedule'));
Vue.component('my-bank-statement', require('./components/shared/BankStatementRequest'));
Vue.component('mono-statement', require('./components/shared/MonoStatement'));
Vue.component('retry-statements', require('./components/admin/RetryStatements'));
Vue.component('bvn-verificator', require('./components/shared/BVNVerificator'));
Vue.component('investor-transactions', require('./components/shared/InvestorTransactions'));
Vue.component('birthdays', require('./components/shared/Birthdays'));
Vue.component('new-group', require('./components/shared/NewGroup'));


Vue.component('active-targets', require('./components/shared/ActiveTargets'));
Vue.component('timer', require('./components/shared/Timer'));

Vue.component('loan-note', require('./components/shared/LoanNote'));

Vue.component('restructure-loan', require('./components/shared/RestructureLoan.vue'));
Vue.component('card-log', require('./components/shared/CardLog.vue'));

Vue.component('notification', require('./components/shared/Notification.vue'));

Vue.component('withdrawal-request-form', require('./components/shared/WithdrawalRequestForm'));

Vue.component('loan-transfer', require('./components/shared/LoanTransfer'));
Vue.component('loan-bid-update', require('./components/shared/LoanBidUpdate'));
Vue.component('lender-registration', require('./components/shared/LenderRegistration'));
Vue.component('new-user', require('./components/shared/NewUser'));
Vue.component('wallet-fund', require('./components/shared/WalletFund'));
Vue.component('payday-wallet-fund', require('./components/shared/PaydayWalletFund'));
Vue.component('max-request-amount', require('./components/shared/MaxLoanRequestAmount'));
Vue.component('emi-calculator', require('./components/shared/EMICalculator'));
Vue.component('employment-update', require("./components/shared/EmploymentUpdate"));

Vue.component('investor-statistics', require('./components/shared/InvestorStats.vue'));
Vue.component('promissory-investor-statistics', require('./components/shared/PromissoryInvestorStats.vue'));

Vue.component('remita-otp-form', require('./components/shared/RemitaOTPForm.vue'));

Vue.component('resubmit-loan-request', require('./components/shared/ResubmitLoanRequest.vue'));

Vue.component('authority-form-upload', require('./components/shared/AuthorityFormUpload.vue'));

Vue.component('modal', require('./components/Modal.vue'));
Vue.component('file-progress', require('./components/FileProgress.vue'));

Vue.component('lender-upgrade-form', require('./components/LenderUpgradeForm'));

Vue.component('invest', require('./components/investors/Invest'));
Vue.component('bid', require('./components/investors/Bid'));
Vue.component('bid-manager', require('./components/investors/BidManager'));

Vue.component('investor-reset-password', require('./components/investors/InvestorResetPassword'));

Vue.component('employment-display', require('./components/investors/EmploymentDisplay'));
Vue.component('sell-investment', require('./components/investors/SellInvestment'));
Vue.component('investor-registration', require('./components/investors/InvestorRegistration'));

Vue.component('wallet-vault-transfer', require('./components/investors/WalletVaultTransfer'));
Vue.component('setup-investment-profile', require('./components/investors/SetupInvestmentProfile'));

Vue.component('user-display', require('./components/shared/UserDisplay'));
Vue.component('loan-display', require('./components/shared/LoanDisplay'));
Vue.component('test-component', require('./components/TestComponent'));


Vue.component("manage-employer-form", require('./components/shared/ManageEmployerForm'));
Vue.component("collection-plan-juggler", require('./components/shared/CollectionPlanJuggler'));

Vue.component('shared-conversation', require('./components/shared/Conversation'));


Vue.component('treeselect', Treeselect);

Vue.component('click-to-copy', require('./components/shared/ClickToCopy'));

Vue.component('newton-loader', require('./components/shared/NewtonLoader'));
Vue.component('loader1', require('./components/shared/Loader1'));
Vue.component('loader', require('./components/shared/Loader'));
Vue.component('pencil-loader', require('./components/shared/PencilLoader'));
Vue.component('staggered-loader', require('./components/shared/StaggeredLoader'));


Vue.component('affiliate-verification', require('./components/affiliates/Verification'));

Vue.component('report', require('./components/shared/Report'));
Vue.component('report-item', require('./components/shared/ReportItem'));

Vue.component('documents-required', require('./components/DocumentRequired'));
Vue.component('loanrequest-docs', require('./components/admin/LoanDocsRequired'));
Vue.component('loan-migration', require('./components/investors/InvestorLoanMigration'));
Vue.component('loanfund-repayment', require('./components/shared/LoanfundeRepayment'));


import BootstrapVue from 'bootstrap-vue';
import store from './store';
import VueTour from 'vue-tour';
// You could use your own fancy-schmancy custom styles.
// We'll use the defaults here because we're lazy.
import 'vue-tour/dist/vue-tour.css';


Vue.use(VueTour);

Vue.use(BootstrapVue);

const app = new Vue({
    el: '#app',
    store
});