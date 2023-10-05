import * as types from './types';
import Axios from 'axios';

export const updateWallet = (context, amount) => {
    context.commit(types.UPDATE_WALLET, { amount });
};

export const updateEscrow = (context, amount) => {
    context.commit(types.UPDATE_ESCROW, { amount });
}

export const searchBorrowers = ({ commit }, obj) => {
    let params = { query: obj.query, type: obj.type }

    Axios.get('/api/admin/search/borrower/loans', { params }).then((res) => {
        commit('SEARCH_BORROWERS', res.data)
    }).catch((err) => {
        //console.log(err)
    })
}

export const searchRepaymentBorrowers = ({ commit }, obj) => {
    let params = { query: obj.userQuery }
    Axios.get('/api/repayments/borrowers', { params }).then((res) => {
        commit('SET_REPAYMENT_BORROWERS', res.data)
    }).catch((err) => {})
}

export const searchLoanBorrowers = ({ commit }, obj) => {
    let params = { query: obj.userQuery }
    Axios.get(obj.url, { params }).then((res) => {
        commit('SET_LOAN_BORROWERS', res.data)
    }).catch((err) => {})
}

export const emptyBorrowers = ({ commit }) => {
    commit('EMPTY_BORROWERS');
}

export const getBorrowers = ({ commit }) => {
    Axios.get('/api/admin/all/borrower/loans').then((res) => {
        commit('SEARCH_BORROWERS', res.data)
    }).catch((err) => {

        console.log(err)
    })
}

export const setBills = ({ commit }) => {

    return Axios.get('/ucnull/bills/data').then((res) => {

        commit('SET_BILLS', res.data)

    }).catch((err) => {
        // console.log(err)
    })
}

export const setBillCategories = ({ commit }) => {

    return Axios.get('/ucnull/bills/category/all').then((res) => {

        commit('SET_BILL_CATEGORIES', res.data.data)

    }).catch((err) => {
        // console.log(err)
    })
}

export const setTargets = ({ commit }) => {

    return Axios.get('/ucnull/targets/data').then((res) => {
        commit('SET_TARGETS', res.data)
    }).catch((err) => {

    })
}


export const searchTransactions = ({ commit }, obj) => {

    let params = obj;

    return Axios.get('/ucnull/gateway-transactions/data', { params }).then((res) => {

        commit('SET_TRANSACTIONS', res.data)

    }).catch((err) => {
        // console.log(err)
    })
}