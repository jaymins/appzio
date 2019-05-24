<?php

namespace packages\actionMgallery\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMgallery\Views\View as ArticleView;
use packages\actionMgallery\Models\Model as ArticleModel;

class Swiper extends BootstrapController
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
        $id = $this->model->getItemId() ? $this->model->getItemId() : 0;

        if(isset($_REQUEST['swid']) AND !isset($_REQUEST['world_ending'])){
            $this->model->deleteImage($_REQUEST['swid']);
        }

        $data['item'] = $id;
        $data['images'] = $this->model->getImages();
        return ['Swiper', $data];
    }

}
