import * as types from './types';

export const mutations = {

    [types.UPDATE_WALLET](state, { amount }) {
        state.wallet += parseFloat(amount);
    },

    [types.UPDATE_ESCROW](state, { amount }) {
        state.escrow += parseFloat(amount);
    },

    [types.SEARCH_BORROWERS](state, borrowers) {

        state.borrowers = borrowers
    },


    [types.EMPTY_BORROWERS](state, borrowers) {
        state.borrowers = []
    },

    SET_REPAYMENT_BORROWERS(state, borrowers) {
        state.repaymentBorrowers = borrowers
    },
    SET_LOAN_BORROWERS(state, borrowers) {
        state.loanRequestBorrowers = borrowers
    },

    SET_BILLS(state, bills) {
        state.bills = bills;
    },

    SET_BILL_CATEGORIES(state, categories) {
        state.billcategories = categories;
    },

    SET_TARGETS(state, targets) {
        state.targets = targets;
    },

    SET_TRANSACTIONS(state, transactions) {

        state.transactions = transactions
    }
};