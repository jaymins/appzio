<?php

namespace packages\actionMprofile\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMprofile\Views\Edit as ArticleView;
use packages\actionMprofile\Models\Model as ArticleModel;

class Controller extends BootstrapController
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault()
    {
        $data = array();
        return ['View', $data];
    }

}
