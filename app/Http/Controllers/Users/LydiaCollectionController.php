<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\Lydia\LydiaService;
use Illuminate\Http\Request;

class LydiaCollectionController extends Controller
{
    protected $lydiaService;

    public function __construct(LydiaService $lydiaService)
    {
        $this->lydiaService = $lydiaService;
    }

    function createMandate($mandateData = []) {
        return $this->lydiaService->createMandate($mandateData);
    }
}
