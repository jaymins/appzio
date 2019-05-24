<?php

namespace packages\actionMbooking\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMbooking\Views\View as ArticleView;
use packages\actionMbooking\Models\Model as ArticleModel;

class Location extends BootstrapController
{

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     *
     * @return array
     */
    public function actionDefault()
    {
        $id = $this->getMenuId();

        $variables = \AeplayVariable::getArrayOfPlayvariables($id);
        $lat = isset($variables['lat']) ?: 42.69751;
        $lon = isset($variables['lon']) ?: 23.32415;

        return ['Location', compact('lat', 'lon')];
    }

}
