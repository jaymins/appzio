<?php

namespace packages\actionMtasks\Controllers;
use Bootstrap\Controllers\BootstrapController;
use function is_numeric;
use packages\actionMtasks\Views\View as ArticleView;
use packages\actionMtasks\Models\Model as ArticleModel;

class Tasklist extends BootstrapController {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault(){
        $data = array();
        $this->model->configureMenu();
        $data['menuid'] = 'no'.$this->getMenuId();
        $data['tasks'] = $this->model->getCompleteTasksInfo();
        return ['Tasklist',$data];
    }

    public function actionSaveproof(){

        if(is_numeric($this->getMenuId())){
            if($this->model->validateProof()){
                $this->model->configureMenu();
                $data['tasks'] = $this->model->getCompleteTasksInfo();
                $this->model->flushActionRoutes();
                return ['Tasklist',$data];
            }
        }

        return ['Addproof',$this->setMyProofData()];

    }


    public function setMyProofData(){
        $id = $this->model->getItemId();
        $data['task_id'] = $id;
        $data['task_data'] = $this->model->getTaskWithRelations($id);
        $data['tasks_info'] = $this->model->getCompleteTasksInfo($id);
        $data['reject_note'] = $this->model->getLastRejectNote($id);
        $data['cart_items'] = $this->model->getCartItems(false,$id);
        $data['cart'] = $this->model->getCart(false,$id);
        $data['cart_total'] = $this->model->getCartTotal(false,$id);
        $data['task_cart_info'] = $this->model->getCartDataTask($id);
        $data['task'] = $this->model->getTaskById($id);
        return $data;
    }



}
