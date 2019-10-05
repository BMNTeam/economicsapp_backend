<?php

namespace App\AddData\Municipality;

use App\Entity\Culture;
use Symfony\Component\HttpFoundation\Request;

class AddMunicipalityRequest {

    public $culture_id;
    public $statTypeId;
    public $farm_category_id;
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
        $this->culture_id = $data->cultureId;
        $this->statTypeId = $data->statTypeId;
        $this->farm_category_id = $data->farmCategoryId;
        $this->data =  $data->data;
    }
}

