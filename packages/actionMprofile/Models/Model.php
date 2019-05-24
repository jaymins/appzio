<?php


namespace packages\actionMprofile\Models;
use Bootstrap\Models\BootstrapModel;
use function is_numeric;
use function str_replace;
use function stristr;
use function strtolower;
use function ucwords;

class Model extends BootstrapModel {

    public $validation_errors;

    public function saveProfile()
    {
        foreach ($this->submitvariables as $id => $variable) {
            $this->saveVariable($id, $variable);
        }
    }
}
