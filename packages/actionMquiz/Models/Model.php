<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMquiz\Models;
use Bootstrap\Models\BootstrapModel;

class Model extends BootstrapModel {

    /**
     * This variable doesn't actually need to be declared here, but but here for documentation's sake.
     * Validation erorr is an array where validation errors are saved and can be accessed by controller,
     * view and components.
     */

    public $validation_errors;
    public $output;
    public $editId;
    public $quiz_id;
    public $questions;
    public $quiz_name;

    public function getAllQuizzes()
    {
        return @QuizModel::model()->findAllByAttributes(['app_id' => $this->appid,'active' => 1,'show_in_list' => 1]);
    }

    public function getQuizName(){
        $quiz = @QuizModel::model()->findByPk($this->quiz_id);
        if(isset($quiz->name)){
            return $quiz->name;
        }

        return '';
    }

    public function getQuestions($id){
        $questions = @QuizSetModel::model()->inorder()->with('question','question.option')->findAllByAttributes(['quiz_id' => $id]);
        $this->questions = $questions;
        return $questions;
    }

    public function setTitle($id,$context='questionlist')
    {
        $this->rewriteActionConfigField('backarrow', 1);

        if($context == 'question'){
            $info = @QuizQuestionModel::model()->findByPk($id);
            $name = isset($info->title) ? $info->title : false;
        } else {
            $info = @QuizModel::model()->findByPk($id);
            $name = isset($info->name) ? $info->name : false;
        }

        if($name){
            $name = $this->localize($name);
            $this->rewriteActionConfigField('subject', $name);
            $this->rewriteActionField('subject', $name);
        }

        return $name;
    }

    public function getQuestion($id){
        return @QuizQuestionModel::model()->with('option')->findByPk($id);
    }

    public function saveAnswer()
    {

        if(stristr($this->getMenuId(),'phase-')){
            $phase = str_replace('phase-', '', $this->getMenuId());


            if(isset($this->questions[$phase])){
                $question = $this->questions[$phase];
                if(isset($question->quiz_id)){
                    $variables = $this->submitvariables;
                    $value = array_pop($variables);
                    if(empty($value) && !$this->getConfigParam('flow_skip')) {
                        return false;
                    }
                    $quiz = QuizModel::model()->findByPk($question->quiz_id);
                    if($quiz->save_to_database == 1){
                        $questionid = $this->questions[$phase]->id;

                        if(count($this->submitvariables) == 1){
                            $value = array_pop($this->submitvariables);

                            $answer = QuizQuestionAnswerModel::model()->findByAttributes(
                                ['play_id' => $this->playid, 'answer_id' => $value,'question_id' => $questionid]);

                            if($answer){
                                $answer->answer_id = $value;
                                $answer->update();
                            } else {
                                $answer = new QuizQuestionAnswerModel();
                                $answer->play_id = $this->playid;
                                $answer->answer_id = $value;
                                $answer->question_id = $questionid;
                                $answer->insert();
                            }
                        } else {
                            QuizQuestionAnswerModel::model()->deleteAllByAttributes(
                                ['play_id' => $this->playid,'question_id' => $questionid]);

                            foreach($this->submitvariables as $var){
                                if($var){
                                    $answer = new QuizQuestionAnswerModel();
                                    $answer->play_id = $this->playid;
                                    $answer->answer_id = $var;
                                    $answer->question_id = $questionid;
                                    $answer->insert();

                                }
                            }
                        }
                    } else {
                        $this->saveAllSubmittedVariables();
                    }
                }
            }

            return true;

        }


    }


}