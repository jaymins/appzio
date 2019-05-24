<?php

namespace packages\actionMquiz\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMquiz\Views\View as ArticleView;
use packages\actionMquiz\Models\Model as ArticleModel;

class Questionlist extends BootstrapController
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
        $id = $this->model->getMenuId() ? $this->model->getMenuId() : $this->model->sessionGet('question_list_id');

        if($id){
            $this->model->setTitle($id);
            $this->model->sessionSet('question_list_id', $id);
            $params['list'] = $this->model->getQuestions($id);
        }

        return ['Questionlist', $params];
    }

}
