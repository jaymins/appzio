<?php

namespace packages\actionMgallery\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMgallery\Views\View as ArticleView;
use packages\actionMgallery\Models\Model as ArticleModel;

class Controller extends BootstrapController
{

    /**
     * @var ArticleView
     */
    public $view;

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

        $data = [];
        $this->model->saveImages();
        $data['images'] = $this->model->getImages();
        return ['View', $data];
    }

}
