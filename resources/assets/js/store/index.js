import Vue from 'vue';
import Vuex from 'vuex';

import { mutations } from './mutations';
import * as actions from './actions';
import * as getters from './getters';


const state = {

    wallet: 0.00,
    escrow: 0.00,
    borrowers: [],
    bills: [],
    billcategories: [],
    targets: [],
    refunds: [],
    transactions: [],
    loans: [],
    repaymentBorrowers: [],
    loanRequestBorrowers: []
};

Vue.use(Vuex);

export default new Vuex.Store({
    state,
    getters,
    mutations,
    actions
});