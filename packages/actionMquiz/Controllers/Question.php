<?php

namespace packages\actionMquiz\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMquiz\Views\View as ArticleView;
use packages\actionMquiz\Models\Model as ArticleModel;

class Question extends BootstrapController
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
        $params = array();

        if($this->getMenuId() == 'save'){
            $this->model->saveAllSubmittedVariables();
        }

        if($this->model->getMenuId() AND is_numeric($this->model->getMenuId())){
            $id = $this->model->getMenuId() ? $this->model->getMenuId() : $this->model->sessionGet('question_id');
            $this->model->sessionSet('question_id', $id);
        } else {
            $id = $this->model->sessionGet('question_id');
        }

        if($id){
            $this->model->setTitle($id,'question');
            $params['question'] = $this->model->getQuestion($id);
        }

        return ['Question', $params];
    }

}
