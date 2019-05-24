<?php

namespace packages\actionMgallery\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMgallery\Views\View as ArticleView;
use packages\actionMgallery\Models\Model as ArticleModel;

class Edit extends BootstrapController
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

        if(isset($_REQUEST['imageswiper'])){
            $id = $_REQUEST['imageswiper'];
        }

        $data['item'] = $id;
        $data['images'] = $this->model->getImages();
        return ['Edit', $data];
    }

    public function actionSave(){
        $this->model->updateImage($this->getMenuId());
        $this->no_output = true;
        return ['Edit', []];
    }

    public function actionShare()
    {
        $data = [];

        if(isset($_REQUEST['imageswiper'])){
            $id = $_REQUEST['imageswiper'];
        }

        $data['item'] = $id;
        $data['images'] = $this->model->getImages();
        return ['Share', $data];
    }


}
