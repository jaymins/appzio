<?php

namespace packages\actionMlogin\Controllers;

use Bootstrap\Controllers\BootstrapController;

class Forgot extends BootstrapController
{
    public $view;

    public $model;

    public function actionDefault()
    {
        return ['ForgotPassword'];
    }
}
