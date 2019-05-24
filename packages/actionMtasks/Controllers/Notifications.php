<?php

/**
 * Place for showing notification screens
 */

namespace packages\actionMtasks\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMtasks\Views\View as ArticleView;
use packages\actionMtasks\Models\Model as ArticleModel;

class Notifications extends BootstrapController {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault(){
        $data = array();

        $id = $this->model->getItemParts();
        $command = $id['string'];
        $id = $id['id'];

        switch($command){
            case 'deal_accepted':
                return['Notificationaccept',$data];
                break;

            default:
                return['Notificationaccept',$data];
                break;
        }

        if(stristr($this->getMenuId(),'popup_details_id_')){
            $id = str_replace('popup_details_id_', '', $this->getMenuId());
            $this->model->sessionSet('mode', 'popup_details');
            $this->model->sessionSet('edit_task_id',$id);
            $this->model->sessionSet('task_id',$id);

            $task = $this->model->getTaskById($id);

            //$this->model->sessionSet('adult_id',$task->assignee_id);
            //$this->model->sessionSet('adult_id',$task->assignee_id);

            $data['item_id'] = $id;
            $data['taskinfo'] = $task;
            $data['task_type'] = '{#chores#}';
            $data['task_icon'] = 'icon-task-chores.png';
            $data['task_type_list'] = $this->model->getChoreListForSelect($task->category_name);
            return ['Detailspopup',$data];
        } elseif($this->model->sessionGet('edit_task_id')){
            $id = $this->model->sessionGet('edit_task_id');
            $task = $this->model->getTaskById($id);
            $this->model->sessionSet('task_id',$id);

            $data['item_id'] = $id;
            $data['taskinfo'] = $task;
            $data['task_type_list'] = $this->model->getChoreListForSelect($task->category_name);

            return ['Detailspopup',$data];
        }

        if($this->getMenuId() == 'popup_details' OR $this->model->sessionGet('mode') == 'popup_details'){
            $data = array();
            $this->model->sessionSet('mode', 'popup_details');
            $data['item_id'] = $this->model->getItemId();
            $data['product_id'] = $this->model->sessionGet('productid');
            $data['taskinfo'] = $this->model->getTaskById($this->model->sessionGet('task_id'));
            $data['step'] = 1;
            $data['adults'] = $this->model->getAdults();
            return ['Detailspopup',$data];
        } else {
            return $this->actionChooseadult();
        }
    }






}
