<?php


namespace packages\actionMregister\themes\demoreg\Models;
use packages\actionMregister\Models\Model as BootstrapModel;

class Model extends BootstrapModel {

    public function getMyCountryCode(){
        /* 1. check if user has submitted value for this */
        $code = $this->getSubmittedVariableByName('countrycode');

        /* 2. if not, try if we can fetch the country code */
        if(!$code) {
            $code = $this->getCountryCode();
        }

        /* 3. if still no go, just send empty so that the field type is right */
        if(!is_string($code)){
            $code = '';
        }

        return $code;

    }


}