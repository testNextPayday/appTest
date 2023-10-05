<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanNote;
use Illuminate\Http\Request;
use App\Services\LoanNoteService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Unicredit\Collection\Utilities;

class LoanNoteController extends Controller
{
    //
    
    /**
     * Injects the loan note service
     *
     * @param  mixed $service
     * @return void
     */
    public function __construct(LoanNoteService $service)
    {
        $this->loanNoteService = $service;
    }

    
    /**
     * Stores a loan note
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        try {

            $desc = $request->note;

            $loan = Loan::find($request->loan_id);

            $writer = Utilities::currentlyAuthUser();

            $success = $this->loanNoteService->createNote($writer, $loan, $desc);

            if ($success) {

                return redirect()->back()->with('success', 'Loan note has been created');
            }

        }catch (\Exception $e) {
           
            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('failure', 'An error occured. ');
    }

    
    /**
     * Updates a loan note 
     *
     * @param  mixed $note
     * @param  mixed $request
     * @return void
     */
    public function update(LoanNote $note, Request $request)
    {
        try {

            $data = $request->only(['note']);

            $writer = Utilities::currentlyAuthUser();

            $success = $this->loanNoteService->updateNote($data, $note, $writer);

            if ($success) {

                return redirect()->back()->with('success', 'Loan note has been updated');
            }

        }catch (\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('failure', 'An error occured. ');
    }


    
    /**
     * Delete a loan note
     *
     * @param  mixed $note
     * @return void
     */
    public function delete(LoanNote $note)
    {
        try {

            $writer = Utilities::currentlyAuthUser();

            $success = $this->loanNoteService->deleteNote($note, $writer);

            if ($success) {

                return redirect()->back()->with('success', 'Loan note has been deleted');
            }

        }catch (\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('failure', 'An error occured. ');
    }
}
