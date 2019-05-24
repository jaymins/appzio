<?php

namespace packages\actionMvenues\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMvenues\Views\View as ArticleView;
use packages\actionMvenues\Models\Model as ArticleModel;

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

        if($this->getMenuId() AND $this->getMenuId() != 'save_venue'){
            $this->model->sessionSet('venueid', $this->getMenuId());
            $data['venue'] = $this->model->getVenue($this->getMenuId());
            return ['Edit', $data];
        } elseif($this->getMenuId() == 'save_venue') {
            $this->model->saveVenue(true);
            return ['Done', []];
        } else {
            $data = array();
            return ['Edit', $data];
        }



    }


}
