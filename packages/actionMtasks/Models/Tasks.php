<?php


namespace packages\actionMtasks\Models;
use Bootstrap\Models\BootstrapModel;
use function is_object;
use function str_replace_array;
use packages\actionMnotifications\Models\NotificationsModel;

trait Tasks {

    /* deals related stuff here */

    public function getCompleteTasksInfo($taskid=false,$playid=false){

        if(!$playid){
            $playid = $this->playid;
        }

        $rows = \Yii::app()->db
            ->createCommand($this->taskQuerySql($taskid))
            ->bindValues(array(
                    ':playID' => $playid,
                    ':username' => $this->getVariableId('username'),
                    ':profilepic' => $this->getVariableId('profilepic'),
                )
            )

            ->queryAll();


        $out['countered'] = array();
        $out['proposed'] = array();
        $out['active'] = array();
        $out['completed'] = array();
        $out['expired'] = array();

        if($rows){
            foreach($rows as $task){

                $task['proofs_required'] = $task['deadline'] - $task['start_time'];
                $task['proofs_required'] = ceil($task['proofs_required'] / $task['repeat_frequency']*$task['times_frequency']);

                if (isset($out[$task['status']])) {
                    if(($task['status'] == 'active' OR $task['status'] == 'proposed')
                        AND $task['deadline'] < time()){
                        $out['expired'][] = $task;
                        $update = TasksModel::model()->findByPk($task['id']);
                        $update->status = 'expired';
                        $update->update();
                    } else {
                        $out[$task['status']][] = $task;
                    }
                }

                if($taskid AND isset($rows[0])){
                    return $task;
                }

            }
        }


        return $out;

    }

    public function getLastRejectNote($taskid){
        $out = '';

        $obj = TasksProofModel::model()->findAllByAttributes(array('task_id' => $taskid,'status' => 'rejected'));

        foreach($obj as $note){
            if($note->comment){
                $out = $note->comment;
            }
        }

        return $out;

    }

    private function taskQuerySql($id=false){
        if($id){
            $taskidquery = "AND ae_ext_mtasks.id = $id";
        } else {
            $taskidquery = false;
        }

        $sql = "SELECT username.value as username, profilepic.value as profilepic,
                  ae_ext_mtasks.*,
                  ae_ext_mtasks.id as taskid,
                  ae_ext_mtasks.title as tasktitle,
                  ae_ext_products.photo as productphoto,
                  ae_ext_products.title as producttitle,
                  ae_ext_mtasks_invitations.nickname as nickname,
                  (SELECT count(id) FROM ae_ext_mtasks_proof WHERE task_id = ae_ext_mtasks.id AND ae_ext_mtasks_proof.status = 'accepted') as proofcount,
                  (SELECT count(id) FROM ae_ext_mtasks_proof WHERE task_id = ae_ext_mtasks.id) as totalproofcount
                  FROM ae_ext_mtasks 
                  LEFT JOIN ae_ext_mtasks_invitations ON owner_id = play_id
                  LEFT JOIN ae_ext_products_carts ON ae_ext_mtasks.id = ae_ext_products_carts.task_id
                  LEFT JOIN ae_ext_products ON ae_ext_products_carts.product_id = ae_ext_products.id
                  LEFT JOIN ae_game_play_variable AS username ON ae_ext_mtasks.assignee_id = username.play_id AND username.variable_id = :username
                  LEFT JOIN ae_game_play_variable AS profilepic ON ae_ext_mtasks.assignee_id = profilepic.play_id AND profilepic.variable_id = :profilepic
                  WHERE ae_ext_mtasks.owner_id = :playID
                  $taskidquery
                  GROUP BY ae_ext_mtasks.id";

        return $sql;
    }

    public function getTaskWithRelations($id){
            $sql = $this->taskQuerySql($id);

            $rows = \Yii::app()->db
                ->createCommand($sql)
                ->bindValues(array(
                        ':playID' => $this->playid,
                        ':username' => $this->getVariableId('username'),
                        ':profilepic' => $this->getVariableId('profilepic')
                    )
                )

                ->queryAll();

            if(isset($rows[0])){
                $task = $rows[0];
                $task['proofs_required'] = $task['deadline'] - $task['start_time'];
                $task['proofs_required'] = ceil($task['proofs_required'] / $task['repeat_frequency']*$task['times_frequency']);
                return $task;
            }

            return array();


    }

    public function getActiveTaskCount($playid){
        return TasksModel::model()->with('invitations')->countByAttributes(array('owner_id' => $playid,'status' => 'active'));
    }

    public function getMyTasksChild(){

        $tasks = TasksModel::model()->with('invitations')->findAllByAttributes(array('owner_id' => $this->playid));

        if(!$tasks){
            return array();
        }

        $out['countered'] = array();
        $out['proposed'] = array();
        $out['active'] = array();
        $out['completed'] = array();
        $out['expired'] = array();

        foreach ($tasks as $task){

            if (isset($out[$task->status])) {
                if(($task->status == 'active' OR $task->status == 'proposed')
                    AND $task->deadline < time()){
                    $out['expired'][] = $task;
                    $update = TasksModel::model()->findByPk($task->id);
                    $update->status = 'expired';
                    $update->update();
                } else {
                    $out[$task['status']][] = $task;
                }

                $out[$task->status][] = $task;
            }
        }

        if(isset($out)){
            return $out;
        }

    }


    public function cancelCurrentTask(){
        $id = $this->sessionGet('task_id');
        TasksModel::model()->deleteByPk($id);

        $this->sessionSet('adult_id', '');
        $this->sessionSet('invitation_id', '');
        $this->sessionSet('task_id', '');
    }


    public function confirmCurrentTask(){
        $this->setCurrentTask();

        if(is_object($this->taskobj)){
            $this->taskobj->status = 'proposed';
            $this->taskobj->update();
        }

        if (!$this->sessionGet('isEditing')) {
            $this->transferCart($this->taskobj->id);
        }

        if (isset($this->taskobj->assignee_id) AND $this->taskobj->assignee_id) {

            $this->notifications->addNotification(array(
                'subject' => '{#task_waiting_for_approval#} '. $this->taskobj->title,
                'to_playid' => $this->taskobj->assignee_id,
                'type' => 'new_task',
                'id' => $this->taskobj->id
            ));

        }

        $this->sessionSet('adult_id', '');
        $this->sessionSet('invitation_id', '');
    }


    public function setCurrentTask($id=false){

        if(is_object($this->taskobj)){
            return true;
        }

        if(!$this->sessionGet('task_id') AND !$id){
            return false;
        }

        $id = $id ? $id : $this->sessionGet('task_id');
        $this->taskobj = TasksModel::model()->findByPk((int)$id);

        if($this->taskobj){
            return true;
        }

        return false;
    }

    public function validateAndSaveDetails($popup=false){

        if(!$popup){
            if(!$this->getSubmittedVariableByName('type')){
                $this->validation_errors['type'] = '{#please_choose_the_type#}';
            }
        }

        if(!$this->getSubmittedVariableByName('description')){
            $this->validation_errors['description'] = '{#please_fill_the_description#}';
        }

        if(!$this->getSubmittedVariableByName('how_often')){
            $this->validation_errors['how_often'] = '{#please_define_how_often#}';
        }

        if(!$popup) {
            if (empty($this->getCart())) {
                $this->validation_errors['description'] = '{#cart_can_not_be_empty#}';
            }
        }

        $starting_date = $this->getSubmittedVariableByName('starting_date');
        $ending_date = $this->getSubmittedVariableByName('ending_date');

        if(time()-$starting_date > 86400){
            $this->validation_errors['starting_date'] = '{#date_cant_be_in_the_past#}';
        }

        if(time()-$ending_date > 86400){
            $this->validation_errors['ending_date'] = '{#date_cant_be_in_the_past#}';
        }

        if($starting_date > $ending_date){
            $this->validation_errors['ending_date'] = '{#end_date_needs_to_be_after_start#}';
        }

        if($starting_date == $ending_date){
            $this->validation_errors['ending_date'] = '{#start_date_and_end_date_cant_be_the_same#}';
        }

        if(!$this->sessionGet('category')){
            //$this->validation_errors['general_error'] = '{#please_return_to_previous_step_and_select_the_category_again#}';
        }

        if(!$this->sessionGet('adult_id') AND !$this->sessionGet('invitation_id')){
           // $this->validation_errors['general_error'] = '{#please_return_to_beginning_and_select_adult_again#}';
        }

        if(!$this->validation_errors){
            $id = $this->sessionGet('task_id');

            if($id){
                $new = TasksModel::model()->findByPk($id);
                if(!is_object($new)){
                    $new = new TasksModel();
                    $id = false;
                }
            } else {
                $new = new TasksModel();
            }

            $new->owner_id = $this->playid;

            if(!$popup){
                if($this->sessionGet('adult_id')){
                    $new->assignee_id = $this->sessionGet('adult_id');
                } elseif($this->sessionGet('invitation_id')) {
                    $new->invitation_id = $this->sessionGet('invitation_id');
                }
            }


            $convertFreq = $this->convertFrequencyToSeconds();

            if($this->sessionGet('category')){
                //$new->category_id = $this->sessionGet('category');
                $new->category_name = $this->sessionGet('category');
            }

            if(!$id){
                $new->created_time = time();
            }

            $new->deadline = $ending_date;
            $new->start_time = $starting_date;
            $new->title = $this->getSubmittedVariableByName('type') ? $this->getSubmittedVariableByName('type') : '{#custom#}';
            $new->description = $this->getSubmittedVariableByName('description');
            $new->repeat_frequency = $convertFreq[0];
            $new->times_frequency = $convertFreq[1];

            if($new->comments){
                $new->category_name = 'other';
                $new->title = '{#custom#}';
            }

            $new->status = 'proposed';

            if($id){
                $new->update();
            } else {
                $new->insert();
                $this->sessionSet('task_id',$new->id);
            }

            $this->taskobj = $new;
        }
    }


    private function convertFrequencyToSeconds(){

        $period = 0;
        $times = 0;
        switch($this->getSubmittedVariableByName('how_often')){
            case 'Hour':
                $period = 60*60;
                break;

            case 'Day':
                $period = 60*60*24;
                break;
            case 'Week':
                $period = 60*60*24*7;
                break;

            case '2-Week':
                $period = 60*60*24*7*2;
                break;

            case 'Month':
                $period = 60*60*24*30;
                break;
        }

        switch($this->getSubmittedVariableByName('times')){
            case '1 x':
                $times = 1;
                break;

            case '2 x':
                $times = 2;
                break;
            case '3 x':
                $times = 3;
                break;

            case '4 x':
                $times = 4;
                break;

            case '5 x':
                $times = 5;
                break;
            case '6 x':
                $times = 6;
                break;
            default:
                $times = 0;

        }
        return array($period, $times);
    }

    public function convertSecondsToFrequency($periodSec, $times) {

        $period = '';
        switch ($periodSec) {

            case 60*60:
                $period = 'Hour';
                break;

            case 60*60*24:
                $period = 'Day';
                break;
            case 60*60*24*7:
                $period = 'Week';
                break;

            case 60*60*24*7*2 :
                $period = '2-Week';
                break;

            case 60*60*24*30:
                $period = 'Month';
                break;
            default:
                $period = 'Indefinite';

        }

        return $times. ' x ' . $period;
    }

    public function getMyInvitations(){

        $invites = TasksInvitationsModel::model()->findAllByAttributes(
            array(
                'email' => $this->getSavedVariable('email'),
                'status' => 'invited'));

        if(!$invites){
            return array();
        }

        $out = [];

        foreach ($invites as $invite) {
            $by =  $this->foreignVariablesGet($invite['play_id']);
            $out[] = [
                'invite' => $invite,
                'by' => $by
            ];
        }

        return $out;

    }

    public function updateInvitation($id, $status) {
        $invitation = TasksInvitationsModel::model()->findByPk($id);
        $invitation->status = $status;
        $invitation->invited_play_id = $this->playid;
        $invitation->update();

        if ($status == 'accept') {
            $criteria = new \CDbCriteria();
            $criteria->condition = 'invitations.invited_play_id = :playId';
            $criteria->params = array('playId' => $this->playid);
            $tasks = TasksModel::model()->with('invitations')->together()->findAllByAttributes(
                array(
                    'status' => 'proposed'
                ),
                $criteria
            );

            if($tasks){
                foreach ($tasks as $task) {
                    if (!$task->assignee_id) {
                        $task->assignee_id = $this->playid;
                        $task->update();
                    }
                }
            }

            $this->notifications->addNotification(array(
                'subject' => $this->getSavedVariable('username') . ' {#has_accepted_your_invitation_to_earnster#}',
                'to_playid' => $invitation->play_id,
                'type' => 'invitation_accepted',
            ));
        }

        $this->sessionSet('invite_id', '');
        $this->sessionSet('action_taken', '');
    }

    public function transferCart($taskid){
        $sql = "UPDATE ae_ext_products_carts SET cart_status = 'task', task_id = :taskID WHERE play_id = :playID AND cart_status = 'cart'";
        \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':taskID' => $taskid,
                ':playID' => $this->playid))
            ->query();
    }

    public function getPendingDeals() {

        $tasks = TasksModel::model()->with('invitations')->together()->findAllByAttributes(
            array(
                'status' => 'proposed',
                'assignee_id' => $this->playid
            )
        );

        if(!$tasks){
            return array();
        }
        $out = [];

        foreach ($tasks as $task) {
            $by =  $this->foreignVariablesGet($task['owner_id']);
            $out[] = [
                'task' => $task,
                'by' => $by,
                'cart' => $this->getCartByTask($task['id'])
            ];
        }

        return $out;
    }

    public function getTaskById($id) {
        return TasksModel::model()->with()->findByPk($id);
    }

    public function activateCurrentTask() {
        $deal_id = $this->sessionGet('deal_id');
        $task = $this->getTaskById($deal_id);

        $task->status = 'active';
        $task->update();

        $parent = $this::getVariableContent($task->assignee_id);
        $nickname = isset($parent['username']) ? $parent['username'] .' ' : '';

        // .$task->title

        $this->notifications->addNotification(array(
            'subject' => $nickname .'{#has_accepted_your_deal#}',
            'to_playid' => $task->owner_id,
            'type' => 'task_accepted',
            'id' => $deal_id
        ));

    }

    public function declineCurrentTask() {
        $deal_id = $this->sessionGet('deal_id');
        $task = $this->getTaskById($deal_id);

        $task->status = 'decline';
        $task->update();

        $parent = $this::getVariableContent($task->assignee_id);
        $nickname = isset($parent['username']) ? $parent['username'] .' ' : '';

        $this->notifications->addNotification(array(
            'subject' => $nickname .'{#has_rejected_your_deal#}',
            'to_playid' => $task->owner_id,
            'type' => 'task_declined',
            'id' => $deal_id
        ));

    }

    public function getCountApprovals() {
        $deals = $this->getPendingDeals();
        $invites = $this->getMyInvitations();
        $proofs = $this->getPendingProofs();

        return count($deals) + count($invites) + count($proofs);
    }

    public function getAllTasksParent() {
        $rows = \Yii::app()->db
            ->createCommand($this->taskParentQuerySql())
            ->bindValues(array(
                    ':playID' => $this->playid,
                    ':username' => $this->getVariableId('username'),
                    ':profilepic' => $this->getVariableId('profilepic'),
                )
            )

            ->queryAll();

        $out['proposed'] = array();
        $out['shipped'] = array();
        $out['active'] = array();
        $out['completed'] = array();
        $out['expired'] = array();

        if($rows){
            foreach($rows as $task){

                $task['proofs_required'] = $task['deadline'] - $task['start_time'];
                $task['proofs_required'] = ceil($task['proofs_required'] / $task['repeat_frequency']*$task['times_frequency']);

                if($task['status'] == 'proposed'){

                    if($task['deadline'] < time()){
                        $out['expired'][] = $task;
                        $update = TasksModel::model()->findByPk($task['id']);
                        $update->status = 'expired';
                        $update->update();
                        $out['expired'][] = $task;
                    } else {
                        $out['proposed'][] = $task;
                    }
                }

                if($task['status'] == 'shipped'){
                    $out['shipped'][] = $task;
                }

                if($task['status'] == 'active'){
                    if($task['deadline'] < time()){
                        $out['expired'][] = $task;
                        $update = TasksModel::model()->findByPk($task['id']);
                        $update->status = 'expired';
                        $update->update();
                        $out['expired'][] = $task;
                    } else {
                        $out['active'][] = $task;
                    }
                }

                if($task['status'] == 'completed'){
                    $out['completed'][] = $task;
                }

                if($task['status'] == 'expired'){
                    $out['expired'][] = $task;
                }
            }
        }


        return $out;
    }

    private function taskParentQuerySql($id=false){
        if($id){
            $taskidquery = 'AND ae_ext_mtasks.id = :taskID';
        } else {
            $taskidquery = false;
        }

        $sql = "SELECT username.value as username, profilepic.value as profilepic,
                  ae_ext_mtasks.*,
                  ae_ext_mtasks.id as taskid,
                  ae_ext_mtasks.title as tasktitle,
                  ae_ext_products.photo as productphoto,
                  ae_ext_products.title as producttitle,
                  ae_ext_products.price as productprice,
                  (SELECT count(id) FROM ae_ext_mtasks_proof WHERE task_id = ae_ext_mtasks.id AND status = 'accepted') as proofcount
                  FROM ae_ext_mtasks 
                  LEFT JOIN ae_ext_mtasks_invitations ON ae_ext_mtasks.owner_id = ae_ext_mtasks_invitations.play_id
                  LEFT JOIN ae_ext_products_carts ON ae_ext_mtasks.id = ae_ext_products_carts.task_id
                  LEFT JOIN ae_ext_products ON ae_ext_products_carts.product_id = ae_ext_products.id
                  LEFT JOIN ae_game_play_variable AS username ON ae_ext_mtasks.owner_id = username.play_id AND username.variable_id = :username
                  LEFT JOIN ae_game_play_variable AS profilepic ON ae_ext_mtasks.owner_id = profilepic.play_id AND profilepic.variable_id = :profilepic
                  WHERE ae_ext_mtasks.assignee_id = :playID
                  $taskidquery
                  GROUP BY ae_ext_mtasks.id";

        return $sql;
    }

    public function getTaskWithRelationsParent($id){
        $sql = $this->taskParentQuerySql($id);

        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                    ':playID' => $this->playid,
                    ':username' => $this->getVariableId('username'),
                    ':profilepic' => $this->getVariableId('profilepic'),
                    ':taskID' => $id
                )
            )
            ->queryAll();

/*        echo('playid:'.$this->playid.'<br>');
        echo('username:'.$this->getVariableId('username').'<br>');
        echo('profilepic:'.$this->getVariableId('profilepic').'<br>');
        echo('taskID:'.$id.'<br>');

        print_r($sql);die();

        $sql = "
        SELECT username.value as username, 
            profilepic.value as profilepic, 
            ae_ext_mtasks.*, 
            ae_ext_mtasks.id as taskid, 
            ae_ext_mtasks.title as tasktitle, 
            ae_ext_products.photo as productphoto, 
            ae_ext_products.title as producttitle, 
            ae_ext_products.price as productprice, 
            (SELECT count(id) FROM ae_ext_mtasks_proof WHERE task_id = ae_ext_mtasks.id AND status = 'accepted') as proofcount 
            FROM ae_ext_mtasks 
            LEFT JOIN ae_ext_mtasks_invitations ON ae_ext_mtasks.owner_id = ae_ext_mtasks_invitations.play_id 
            LEFT JOIN ae_ext_products_carts ON ae_ext_mtasks.id = ae_ext_products_carts.task_id 
            LEFT JOIN ae_ext_products ON ae_ext_products_carts.product_id = ae_ext_products.id 
            LEFT JOIN ae_game_play_variable AS username ON ae_ext_mtasks.owner_id = username.play_id AND username.variable_id = 12571 
            LEFT JOIN ae_game_play_variable AS profilepic ON ae_ext_mtasks.owner_id = profilepic.play_id AND profilepic.variable_id = 12512 
            WHERE ae_ext_mtasks.assignee_id = 34122 AND ae_ext_mtasks.id = 120 GROUP BY ae_ext_mtasks.id
        ";*/

        if(isset($rows[0])){
            $rows[0]['proofs_required'] = $rows[0]['deadline'] - $rows[0]['start_time'];
            $rows[0]['proofs_required'] = ceil($rows[0]['proofs_required'] / $rows[0]['repeat_frequency']*$rows[0]['times_frequency']);
            return $rows[0];
        }

        return array();
    }

    public function completeTask($id) {
        $task = $this->getTaskById($id);

        $task->status = 'completed';
        $task->update();

/*        $this->notifications->addNotification(array(
            'subject' => '{#your_final_task_has_been_approved#}',
            'to_playid' => $task->owner_id,
            'type' => 'task_completed',
            'id' => $id
        ));*/

    }

    public function shipTask($id) {
        $task = $this->getTaskById($id);

        $task->status = 'shipped';
        $task->update();

        $this->notifications->addNotification(array(
            'subject' => '{#your_item_has_been_purchased#}',
            'to_playid' => $task->owner_id,
            'type' => 'task_shipped',
            'id' => $id
        ));


    }

    public function saveCounterTask($id) {

        $task = $this->getTaskById($id);
        $comment = $this->getSubmittedVariableByName('comment');

        if(!$comment){
            $this->validation_errors['comment'] = '{#please_provide_a_comment#}';
            return false;
        }


        $task->status = 'countered';
        $task->comments = $comment;
        $task->update();

        $parent = $this::getVariableContent($task->assignee_id);
        $nickname = isset($parent['username']) ? $parent['username'] .' ' : '';

        $this->notifications->addNotification(array(
            'subject' => $nickname .'{#has_countered_your_deal#}',
            'to_playid' => $task->owner_id,
            'type' => 'task_countered',
            'id' => $id
        ));

        return true;

    }
}
