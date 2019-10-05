<?php

namespace App\AddData\Municipality;

class MunicipalityWithData {
    public $id;
    public $name;
    public $value;

    /**
     * MunicipalityWithData constructor.
     * @param $id
     * @param $name
     * @param $value
     */
    public function __construct($id, $name, $value)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
    }
}