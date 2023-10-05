    <div class="row">
        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_borrowers" value="true"
                    {{ $staff->manages('borrowers') ? 'checked': '' }}>&nbsp;Manage Borrowers
            </label>
        </div>
        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_investors" value="true"
                    {{ $staff->manages('investors') ? 'checked': '' }}>&nbsp;Manage Investors
            </label>
        </div>
        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_affiliates" value="true"
                    {{ $staff->manages('affiliates') ? 'checked': '' }}>&nbsp;Manage Affiliates (Supervisor)
            </label>
        </div>
        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_repayments" value="true"
                    {{ $staff->manages('repayments') ? 'checked': '' }}>&nbsp;Manage Loan Repayment Upload
            </label>
        </div>
        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_loan_request" value="true"
                    {{ $staff->manages('loan_request') ? 'checked': '' }}>&nbsp;Manage Loan Request Approval
            </label>
        </div>
        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_settlements" value="true"
                    {{ $staff->manages('settlements') ? 'checked': '' }}>&nbsp;Manage Loan Settlement Upload
            </label>
        </div>
        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_loan_transactions" value="true"
                    {{ $staff->manages('loan_transactions') ? 'checked': '' }}>&nbsp;Manage Loan Transaction Upload
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_loan_restructuring" value="true"
                    {{ $staff->manages('loan_restructuring') ? 'checked': '' }}>&nbsp;Manage Loan Restructuring
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_loanCardSweeping" value="true"
                    {{ $staff->manages('loanCardSweeping') ? 'checked': '' }}>&nbsp;Manage Loan Sweeping
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_loan_request_setup" value="true"
                    {{ $staff->manages('loan_request_setup') ? 'checked': '' }}>&nbsp;Manage Loan Setup
            </label>
        </div>


        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_bills" value="true"
                    {{ $staff->manages('bills') ? 'checked': '' }}>&nbsp;Manage Bills
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_support" value="true"
                    {{ $staff->manages('support') ? 'checked': '' }}>&nbsp;Customer Support
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_followup_investors" value="true"
                    {{ $staff->manages('followup_investors') ? 'checked': '' }}>&nbsp;Follow Up Investors
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_followup_users" value="true"
                    {{ $staff->manages('followup_users') ? 'checked': '' }}>&nbsp;Follow Up Users
            </label>
        </div>        

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_edit_loan_request" value="true"
                    {{ $staff->manages('edit_loan_request') ? 'checked': '' }}>&nbsp;Edit Loan Request
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_assign_loan_request" value="true"
                    {{ $staff->manages('assign_loan_request') ? 'checked': '' }}>&nbsp;Assign Loan Request
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_promissory_notes" value="true"
                    {{ $staff->manages('promissory_notes') ? 'checked': '' }}>&nbsp;Manage Payday Note
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline text-danger">
                <input type="checkbox" name="manage_loan_disbursement" value="true"
                    {{ $staff->manages('loan_disbursement') ? 'checked': '' }}>&nbsp;Loan Disbursement
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_approve_refunds" value="true"
                    {{ $staff->manages('approve_refunds') ? 'checked': '' }}>&nbsp;Manage Refund Approval
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_withdrawal_approval" value="true"
                    {{ $staff->manages('withdrawal_approval') ? 'checked': '' }}>&nbsp;Manage Withdrawal Approval
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_approve_bills" value="true"
                    {{ $staff->manages('approve_bills') ? 'checked': '' }}>&nbsp; Manage Bill Approval
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_salary_payment" value="true"
                    {{ $staff->manages('salary_payment') ? 'checked': '' }}>&nbsp; Manage Staff Salary
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_approve_repayment" value="true"
                    {{ $staff->manages('approve_repayment') ? 'checked': '' }}>&nbsp; Approve Repayment
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_upgrade_user" value="true"
                    {{ $staff->manages('upgrade_user') ? 'checked': '' }}>&nbsp; Upgrade User
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_view_report" value="true"
                    {{ $staff->manages('view_report') ? 'checked': '' }}>&nbsp; View Report
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_confirm_settlement" value="true"
                    {{ $staff->manages('confirm_settlement') ? 'checked': '' }}>&nbsp; Confirm Settlement
            </label>
        </div>

        <div class="form-group col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="manage_borrowers_group" value="true"
                    {{ $staff->manages('borrowers_group') ? 'checked': '' }}>&nbsp; Group Borrowers
            </label>
        </div>

    </div>