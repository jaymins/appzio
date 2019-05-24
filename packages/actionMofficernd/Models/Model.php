<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMofficernd\Models;
use Bootstrap\Models\BootstrapModel;
use yii\db\Exception;

class Model extends BootstrapModel {

    public $validation_errors;
    public $errors;
    public $current_space;

    public $api;

    use Authentication;
    use Stripe;

    public function __construct($obj)
    {
        parent::__construct($obj);
        $this->api = new OfficeRNDApi();
    }

    public function loginUser()
    {
        $username = $this->getSubmittedVariableByName('username');
        $password = $this->getSubmittedVariableByName('password');

        $this->api->loginUser($username, $password);

        if($this->api->errors){
            $this->validation_errors['username'] = implode(', ', $this->api->errors);
        }

    }


}
