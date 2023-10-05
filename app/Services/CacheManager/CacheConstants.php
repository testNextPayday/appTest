<?php
namespace App\Services\CacheManager;


class CacheConstants 
{

    const A_LN_BALANCES = 'active_loan_balances';

    const IN_A_LN_BALANCES = 'in_active_loan_balances';

    const F_LN_BALANCES = 'fulfilled_loan_balances';

    const T_LN_BALANCES = 'transferred_loan_balances';

    const V_LN_BALANCES = 'void_loan_balances';

    const MG_LN_BALANCES = 'managed_loan_balances';

    const PORT_FOLIO_SIZE = 'all_investors_portofilio_size';

    const IN_WALLET_BALANCES = 'all_investors_wallet_balance';

    const USER_WALLET_BALANCES = 'all_borrowers_wallet_balance';

    const ESCROW_WALLET_BALANCES = 'all_borrowers_escrow_balance';

    const A_PAYDAY_NOTE_SUM = 'summation_of_active_payday_notes';

    const A_PAYDAY_NOTE_CURRENT_SUM = 'summation_of_active_payday_notes_current_value';

    const BILL_CAT_STATS = 'bill_categories_statistics';
}