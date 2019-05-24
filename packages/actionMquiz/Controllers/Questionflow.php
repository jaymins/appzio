<?php

namespace packages\actionMquiz\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMquiz\Views\View as ArticleView;
use packages\actionMquiz\Models\Model as ArticleModel;

class Questionflow extends BootstrapController
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
        $params = array('subject' => '');
        $id = $this->model->getConfigParam('quiz');

        if(!$id AND is_numeric($this->getMenuId())) {
            $id = $this->model->getItemId();
        }

        if(!$id){
            $id = $this->model->getItemId(true);
        }

        if($id){
            $this->model->quiz_id = $id;
            $params['list'] = $this->model->getQuestions($id);
        }

        $params['phase'] = $this->model->sessionGet('quiz_phase') ? $this->model->sessionGet('quiz_phase') : 0;

        if(stristr($this->getMenuId(),'phase-')){

            if($this->model->saveAnswer()) {
                $phase = str_replace('phase-', '', $this->getMenuId());
                if($phase == 0) {
                    return ['QuestionflowCompleted', $params];
                }
                else {
                    $params['phase'] = (int)$phase;
                }

            }
            else {
                $params['error'] = '{#please_select_an_answer#}.';

            }

        }
        
        $this->model->sessionSet('quiz_phase', $params['phase']);

        if($id){
            $phase = $params['phase']-1;
            $params['list'] = $this->model->getQuestions($id);

            if(isset($params['list'][$phase]->question->id)){
                //$this->model->rewriteActionField('subject',$params['list'][$phase]->question->variable_name);
                $question_id = $params['list'][$phase]->question->id;
                $this->model->setTitle($question_id,'question');
            }

            $params['subject'] = $this->model->getQuizName();
        }

        return ['Questionflow', $params];
    }

    public function actionUpdatepivot(){
        $this->model->saveVariable('recreate_pivot', 1);
        $this->no_output = true;
        return ['View',[]];
    }

}
