<?php

/**
 * We enrich the notifications with correct data for opening actions, icons etc.
 */

namespace packages\actionMnotifications\themes\example\Models;

class NotificationsModel extends \packages\actionMnotifications\Models\NotificationsModel {


    public function handleType($obj,$type,$id=false){
        switch($type){

            /* to adult */
            case 'new_task':
                $obj->icon = '001-give-card.png';
                break;

            case 'invitation':
                $obj->icon = 'mreg-icon-mail.png';
                break;

            case 'proof_added':
                $obj->icon = '001-give-card.png';
                break;

            /* to teen */
            case 'task_completed':
                $obj->icon = '006-trophy.png';
                $obj->action_id = $this->model->getActionidByPermaname('dealnotifications');
                $obj->action_param = 'task_completed_'.$id;
                break;

            case 'task_declined':
                $obj->icon = '005-dislike.png';
                $obj->action_id = $this->model->getActionidByPermaname('dealnotifications');
                $obj->action_param = 'task_declined_'.$id;
                break;

            case 'task_accepted':
                $obj->icon = '007-checked.png';
                $obj->action_id = $this->model->getActionidByPermaname('dealnotifications');
                $obj->action_param = 'task_accepted_'.$id;
                break;

            case 'task_countered':
                $obj->icon = '001-give-card.png';
                $obj->action_id = $this->model->getActionidByPermaname('dealnotifications');
                $obj->action_param = 'task_countered_'.$id;
                break;

            case 'task_shipped':
                $obj->icon = '004-shopping-cart.png';
                $obj->action_id = $this->model->getActionidByPermaname('dealnotifications');
                $obj->action_param = 'task_shipped_'.$id;
                break;

            case 'invitation_accepted':
                $obj->icon = '003-like.png';
                break;

            case 'proof_accepted':
                $obj->icon = '003-like.png';
                $obj->action_id = $this->model->getActionidByPermaname('dealnotifications');
                $obj->action_param = 'proof_accepted_'.$id;
                break;

            case 'final_proof_accepted':
                $obj->icon = '006-trophy.png';
                $obj->action_id = $this->model->getActionidByPermaname('dealnotifications');
                $obj->action_param = 'final_proof_accepted_'.$id;
                break;

            case 'proof_rejected':
                $obj->icon = '002-flash.png';
                $obj->action_id = $this->model->getActionidByPermaname('dealnotifications');
                $obj->action_param = 'proof_rejected_'.$id;
                break;

            default:
                $obj->icon = '007-checked.png';
                break;
        }

        if($id){
            $obj->task_id = $id;
        }

        return $obj;
    }


}
