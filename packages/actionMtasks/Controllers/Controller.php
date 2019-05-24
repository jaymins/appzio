<?php

namespace packages\actionMtasks\Controllers;
use _DiffEngine;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMtasks\Views\View as ArticleView;
use packages\actionMtasks\Models\Model as ArticleModel;
use function str_replace;
use function stristr;

class Controller extends BootstrapController {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $phase;

    public function __construct($obj)
    {
        parent::__construct($obj);
        $this->phase = $this->model->sessionGet('phase') ? $this->model->sessionGet('phase') : 1;

    }

    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault(){
        $this->model->rewriteActionConfigField('hide_scrollbar', 1);
        $function = 'actionPhase'.$this->phase;
        return $this->$function();
    }

    public function actionFlushroutes(){
        $this->model->flushActionRoutes();
        $this->model->sessionSet('phase', 1);
        $this->no_output = true;
        return ['Blank',array()];
    }

    public function actionPhase1(){
        $data['adults'] = $this->model->getAdults();
        $this->model->setPhase(1);
        return ['Adultlist',$data];
    }

    public function actionSetAdult(){
        $this->no_output = true;
        return ['Blank',array()];
    }

    /* aka choose category */
    public function actionPhase2($error=false){
        if($this->model->sessionGet('adult_id') OR $this->model->sessionGet('invitation_id')){
            $data['item_id'] = $this->model->getItemId();
            $data['product_id'] = $this->model->sessionGet('productid');
            $this->model->setPhase(2);
            $data['error'] = $error;
            return ['Choosecategory',$data];
        } else {
            return $this->actionPhase1();
        }
    }

    public function actionSavecategory(){
        $this->no_output = true;
        $this->model->sessionSet('category', $this->getMenuId());
        return ['Blank',array()];
    }

    public function actionSetadultid(){
        $this->no_output = true;
        $this->model->sessionSet('adult_id', $this->getMenuId());
        $this->model->sessionSet('invitation_id', '');
        return ['Blank',array()];
    }

    public function actionSetinvitationid(){
        $this->no_output = true;
        $this->model->sessionSet('invitation_id', $this->getMenuId());
        $this->model->sessionSet('adult_id', '');
        return ['Blank',array()];
    }

    /* aka task details */
    public function actionPhase3(){

        if(!$this->model->sessionGet('category')){
            $this->actionPhase2('{#select_category#}');
        }

        $data['item_id'] = $this->model->getItemId();
        $data['taskinfo'] = $this->model->getTaskById($this->model->sessionGet('task_id'));

        $this->model->setPhase(3);
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

        if($this->getMenuId() == 'dosave'){
            $this->model->validateAndSaveDetails();

            if(!$this->model->validation_errors){
                $this->model->sessionSet('phase', 4);
                $this->model->setRoute('Controller/phase4/');
                return $this->actionPhase4($data);
            }
        }


        return ['Details',$data];
    }

    /* aka summary screen */
    public function actionPhase4($data=array()){
        $this->model->sessionSet('mode','');
        $isEditing = $this->model->sessionGet('isEditing');
        $this->model->setCurrentTask();
        $data['task'] = $this->model->taskobj;
        if ($isEditing) {
            $data['cart'] = $this->model->getCart(null, $this->model->taskobj->id);
            $data['cart_total'] = $this->model->getCartTotal(null, $this->model->taskobj->id);
            $data['cart_items'] = $this->model->getCartItems(null, $this->model->taskobj->id);
        } else {
            $data['cart'] = $this->model->getCart();
            $data['cart_total'] = $this->model->getCartTotal();
            $data['cart_items'] = $this->model->getCartItems();
        }
        $data['adults'] = $this->model->getAdults(true);
        return ['Summary',$data];
    }

    /* aka final summary */
    public function actionPhase5($data=array()){
        $this->model->confirmCurrentTask();

        $taskid = $this->model->sessionGet('task_id') ? $this->model->sessionGet('task_id') : $this->model->sessionGet('show_task_id');
        $this->model->setCurrentTask($taskid);
        $this->model->sessionSet('show_task_id',$taskid);

        $this->model->sessionSet('mode','');
        $this->model->sessionSet('adult_id','');
        $this->model->sessionSet('invitation_id','');
        $this->model->sessionSet('task_id','');
        $this->model->sessionSet('isEditing', '');
        $this->model->sessionSet('phase',5);

        $data['task'] = $this->model->taskobj;
        $data['cart'] = $this->model->getCart(null,$taskid);
        $data['adults'] = $this->model->getAdults(false,true);
        $data['cart_total'] = $this->model->getCartTotal(null,$taskid);
        $data['cart_items'] = $this->model->getCartItems(null,$taskid);
        $this->model->flushActionRoutes();
        return ['Finalsummary',$data];
    }

    /* this is an async operation */
    public function actionCancel(){
        $this->model->sessionSet('show_task_id','');
        $this->model->cancelCurrentTask();
        $this->model->flushActionRoutes();
        $this->model->sessionSet('phase', 1);
        $this->no_output = true;
    }

    /* this is an async operation, used only by the popup (?) */
    public function actionSave(){
        $this->model->rewriteActionConfigField('hide_scrollbar', 1);

        if($this->getMenuId() == 'dosave') {
            $this->model->validateAndSaveDetails(true);
        } else {
            $this->model->confirmCurrentTask();
        }

        if($this->model->validation_errors){
            if($this->model->sessionGet('edit_task_id')) {
                $id = $this->model->sessionGet('edit_task_id');
                $data['item_id'] = $id;
                $data['taskinfo'] = $this->model->getTaskById($id);
                return ['Detailspopup', $data];
            }
        }

        $this->model->sessionSet('edit_task_id','');
        $this->model->flushActionRoutes();
        return ['Closepopup', array()];
    }

    public function actionReset(){
        $this->no_output = true;
        $this->model->sessionSet('phase', 1);
        $this->model->flushActionRoutes();
    }


    public function actionAddtask(){
        $this->model->rewriteActionConfigField('hide_scrollbar', 1);
        return $this->actionPhase1();
    }

    public function actionEditadult(){
        $this->model->rewriteActionConfigField('hide_scrollbar', 1);

        $item = $this->model->getItemId();
        
        if(stristr($item, 'save_')){
            $item = str_replace('save_', '', $item);
        }
        
        $adult = $this->model->getAdultInvitation($item);
        $data['adult'] = $adult;

        if(stristr($this->getMenuId(), 'save_')){

            if($adult){
                $this->model->validateAdult(false,$item);
            } else {
                $this->model->validateAdult();
            }

            if(!$this->model->validation_errors){
                if($adult){
                    $this->model->saveAdultInvitation($adult->email);
                } else {
                    $this->model->saveAdultInvitation(false,$item);
                }

                return $this->actionPhase1();
            }
        }

        return ['Editadult',$data];
    }

    public function actionAddadult(){
        $this->model->rewriteActionConfigField('hide_scrollbar', 1);

        $this->model->validateAdult();
        $data['done'] = false;

        if(!$this->model->validation_errors AND $this->model->getMenuId() == 'new'){
            $data['done'] = true;
            $this->model->saveAdultInvitation();
            $data['adults'] = $this->model->getAdults();
            return ['Adultlist',$data];
        } elseif($this->model->getMenuId() == 'new'){
            $data['adults'] = $this->model->getAdults();
            return ['Adultlist',$data];
        } else {
            $data['done'] = true;
            $data['adults'] = $this->model->getAdults();
            return ['Adultlist',$data];
        }

    }

/*    public function actionChooseadult(){
        $data['adults'] = $this->model->getAdults();

        $data['error'] = '{#please_choose_an_adult_first#}';
        return ['Adultlist',$data];
    }
*/



}
