<?php

namespace App\AddData\Culture;

use App\Entity\Culture;
use Symfony\Component\HttpFoundation\Request;

class AddCultureRequest {

    public $municipality_id;

    public $statTypeId;

    public $data;

    /**
     * @return CulturesByYear[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * AddCultureRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $data= json_decode($request->getContent())->data;
        $this->municipality_id = $data->municipalityId;
        $this->statTypeId =  $data->statTypeId;
        $this->data =  $data->data;
    }
}

