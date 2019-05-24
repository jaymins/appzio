<?php

namespace packages\actionMtasks\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMtasks\Models\TasksModel;
use packages\actionMtasks\Views\View as ArticleView;
use packages\actionMtasks\Models\Model as ArticleModel;
use function str_replace;
use function stristr;

class Popup extends BootstrapController {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;


    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault(){
        $data = array();

        if(stristr($this->getMenuId(),'popup_details_id_')){
            $id = str_replace('popup_details_id_', '', $this->getMenuId());
            $this->model->sessionSet('mode', 'popup_details');
            $this->model->sessionSet('edit_task_id',$id);
            $this->model->sessionSet('task_id',$id);

            $up = TasksModel::model()->findByPk($id);
            $up->description = '';
            $up->update();

            $task = $this->model->getTaskById($id);

            //$this->model->sessionSet('adult_id',$task->assignee_id);
            //$this->model->sessionSet('adult_id',$task->assignee_id);

            $data = $this->setList($data);

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
            $data = $this->setList($data);


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
            $data = $this->setList($data);


            return ['Detailspopup',$data];
        } else {
            return $this->actionChooseadult();
        }
    }

    public function actionChooseadult(){
        $data = array();
        $data['adults'] = $this->model->getAdults(false);
        $data['item_id'] = $this->model->getItemId();
        $data['menuid'] = 'no'.$this->getMenuId();
        $data['product_id'] = $this->model->sessionGet('productid');
        return ['Adultspopup',$data];
    }

    private function setList($data)
    {
        $category = $this->model->sessionGet('category');

        switch($category){
            case 'chores':
                $data['task_type'] = '{#chores#}';
                $data['task_icon'] = 'icon-task-chores.png';
                $data['task_type_list'] = $this->model->getChoreListForSelect('chores');
                break;

            case 'school':
                $data['task_type'] = '{#extra_school_work#}';
                $data['task_icon'] = 'icon-task-school.png';
                $data['task_type_list'] = $this->model->getChoreListForSelect('extra_school_work');
                break;

            case 'community':
                $data['task_type'] = '{#community_service#}';
                $data['task_icon'] = 'icon-task-community.png';
                $data['task_type_list'] = $this->model->getChoreListForSelect('community_service');
                break;


            default:
                $data['task_type'] = '{#other#}';
                $data['task_icon'] = 'icon-task-other.png';
                $data['task_type_list'] = $this->model->getChoreListForSelect('other');
                break;
        }

        return $data;
    }


}
