<?php
namespace App\Services;

use App\Models\LoanNote;


class LoanNoteService
{
    
    /**
     * Create Loan Notes
     *
     * @param  mixed $writer
     * @param  mixed $loan
     * @param  mixed $desc
     * @return void
     */
    public function createNote($writer, $loan, $desc)
    {
        LoanNote::create(
            ['owner_type'=>get_class($writer) ,'owner_id'=>$writer->id, 'loan_id'=> $loan->id, 'note'=> $desc]
        );

        return true;
    }
    
    /**
     * Updates a loan Note created by the user
     *
     * @param  mixed $data
     * @param  mixed $note
     * @param  mixed $user
     * @return void
     */
    public function updateNote($data, LoanNote $note, $user)
    {
        if ($user->id == $note->owner->id) {

            $note->update($data);

            return true;
        }

        throw new \Exception('You cannot update a note you didnt create');
        
    }

    
    /**
     * Deletes a loan note
     *
     * @param  mixed $note
     * @param  mixed $user
     * @return void
     */
    public function deleteNote(LoanNote $note, $user)
    {
        if ($user->id == $note->owner->id) {

            $note->delete();

            return true;
        }
    }


}