<?php
namespace App\Services\Investor;

use App\Models\PromissoryNoteSetting;

trait PromissoryNoteSettingsUpdate
{



    
    /**
     * Creates a particular promissory Note settings
     *
     * @param  mixed $data
     * @return void
     */
    public function createSettings($data)
    {
        PromissoryNoteSetting::create($data);
    }


    
    /**
     * Updates a particular promissory Note Setting
     *
     * @param  mixed $data
     * @return void
     */
    public function updateSettings($data, PromissoryNoteSetting $note)
    {
        $note->update($data);
    }

    
    /**
     * Deletes a particular promissory Note Setting
     *
     * @param  mixed $note
     * @return void
     */
    public function deleteSettings(PromissoryNoteSetting $note)
    {
        $note->delete();
    }
}