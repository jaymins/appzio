<?php

namespace packages\actionMvenues\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMvenues\Views\View as ArticleView;
use packages\actionMvenues\Models\Model as ArticleModel;

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
        $item = null;
        $isLiked = null;
        $id = null;

        if($this->getMenuId() == 'save_venue'){
            $this->model->saveVenue();
            $data['name'] = $this->model->getSubmittedVariableByName('name');
            return ['Done',$data];
        }

        if($this->model->getSubmittedVariableByName('venue_raw_address')){
            $json = @json_decode($this->model->getSubmittedVariableByName('venue_raw_address'),true);

            if(is_array($json)){
                if(isset($json['address'])){
                    $json['address'] = str_replace('?', '', $json['address']);
                }
                $this->model->sessionSet('venue_raw_address', $json);
                return ['Entry',$json];
            }
        }


        return ['View', []];
    }


}
