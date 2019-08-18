<?php

namespace App\AddData\Culture;

class CultureWithData {
    public $id;
    public $name;
    public $value;
    public $culture_type;

    /**
     * CultureWithData constructor.
     * @param $id
     * @param $name
     * @param $value
     * @param $culture_type
     */
    public function __construct($id, $name, $culture_type, $value)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->culture_type = $culture_type;
    }
}