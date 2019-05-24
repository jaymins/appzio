<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMgdpr\Models;
use Bootstrap\Models\BootstrapModel;

class Model extends BootstrapModel {


    public function deleteUser(){
        \Aeplay::model()->deleteByPk($this->playid);
        return true;
    }

}
