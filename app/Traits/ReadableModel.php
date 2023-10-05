<?php
namespace App\Traits;


trait ReadableModel 
{

    
    /**
     * Get human readable model name of model
     *
     * @return void
     */
    public function toHumanReadable()
    {
        $class = get_class($this);
        $arr = explode('\\', $class);

        return end($arr);

    }
}