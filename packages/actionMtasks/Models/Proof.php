<?php


namespace packages\actionMtasks\Models;
use Bootstrap\Models\BootstrapModel;
use packages\actionMnotifications\Models\NotificationsModel;
use function str_replace_array;

trait Proof {

    public $notifications;

    /* deals related stuff here */
    public function validateProof(){

        $description = $this->getSubmittedVariableByName('description');
        $date = time();
        $pic = $this->getSavedVariable('proofimage');

        if(!$description){
            $this->validation_errors['description'] = '{#please_enter_proof_description#}';
            return false;
        }

        $obj = new TasksProofModel();
        $obj->task_id = $this->getItemId();
        $obj->created_date = $date;
        $obj->description = $description;
        $obj->photo = $pic;
        $obj->insert();
        
        $this->saveVariable('proofimage', '');

        $task = TasksModel::model()->findByPk($obj->task_id);
        $parent = $this::getVariableContent($task->owner_id);
        $nickname = isset($parent['username']) ? $parent['username'] .' ' : '';

        if(isset($task->assignee_id)){
            $this->notifications->addNotification(array(
                'subject' => $nickname .' {#has_submitted_task_proof_for#} '.$task->title,
                'to_playid' => $task->assignee_id,
                'type' => 'proof_added',
                'id' => $obj->task_id
            ));

        }

        return true;
    }

    public function getPendingProofs() {

        $criteria = new \CDbCriteria();
        $criteria->condition = 'task.assignee_id = :playId';
        $criteria->params = array('playId' => $this->playid);
        $proofs = TasksProofModel::model()->with('task')->findAllByAttributes(
            array(
                'status' => 'proposed'
            ),
            $criteria
        );

        if(!$proofs){
            return array();
        }
        $out = [];

        foreach ($proofs as $proof) {
            $by =  $this->foreignVariablesGet($proof['task']['owner_id']);
            $out[] = [
                'proof' => $proof,
                'by' => $by,
                'cart' => $this->getCartByTask($proof['task_id'])
            ];
        }

        return $out;
    }

    public function getProofById($id) {
        return TasksProofModel::model()->with('task')->findByPk($id);
    }

    public function acceptCurrentProof() {
        $proofId = $this->sessionGet('proof_id');
        $proof = $this->getProofById($proofId);

        $proof->status = 'accepted';
        $proof->update();

        $taskInfo = $this->getTaskWithRelationsParent($proof->task_id);
        
        $parent = $this::getVariableContent($taskInfo['assignee_id']);
        $nickname = isset($parent['username']) ? $parent['username'] .' ' : '';
        

        if ($taskInfo['proofcount'] >= $taskInfo['proofs_required']) {
            $this->completeTask($proof->task_id);
            $this->updateCartShopping($proof->task_id);
            $this->notifications->addNotification(array(
                'subject' => $nickname. '{#has_accepted_proof_for#} '. $taskInfo['title'],
                'to_playid' => $taskInfo['owner_id'],
                'type' => 'final_proof_accepted',
                'id' => $proof->task_id
            ));

            return true;
        } else {
            $this->notifications->addNotification(array(
                'subject' => $nickname. '{#has_accepted_proof_for#} '. $taskInfo['title'],
                'to_playid' => $taskInfo['owner_id'],
                'type' => 'proof_accepted',
                'id' => $proof->task_id
            ));

        }

        return false;
    }

    public function rejectCurrentProof() {
        $proofId = $this->sessionGet('proof_id');
        $proof = $this->getProofById($proofId);
        $proof->status = 'rejected';
        $proof->update();

        $taskInfo = $this->getTaskWithRelationsParent($proof->task_id);
        $parent = $this::getVariableContent($taskInfo['assignee_id']);
        $nickname = isset($parent['username']) ? $parent['username'] .' ' : '';

        $this->notifications->addNotification(array(
            'subject' => $nickname .'{#has_rejected_task_proof_for#} '. $taskInfo['title'],
            'to_playid' => $taskInfo['owner_id'],
            'type' => 'proof_rejected',
            'id' => $proof->task_id
        ));

    }

    public function checkProofSubmitAvailability($taskId) {

        $task = $this->getTaskById($taskId);
        $criteria = new \CDbCriteria();
        $criteria->condition = 'task.id = :taskId AND t.status <> :status';
        $criteria->params = array('taskId' => $taskId, "status" => "rejected");
        $proofs = TasksProofModel::model()->with('task')->findAll(
            $criteria
        );

        if(!$proofs){
            return true;
        }

        $countProofs = [];
        $countProofsWeek = [];
        $countProofsMonth = [];
        $countProofsHour = [];
        foreach ($proofs as $proof) {

            $created = date('Ymd', $proof->created_date);
            if (!isset($countProofs[$created])) {
                $countProofs[$created] = 1;
            } else {
                $countProofs[$created] ++;
            }

            $createdWeek = date('W', $proof->created_date);
            if (!isset($countProofsWeek[$createdWeek])) {
                $countProofsWeek[$createdWeek] = 1;
            } else {
                $countProofsWeek[$createdWeek] ++;
            }

            $createdMonth = date('m', $proof->created_date);
            if (!isset($countProofsMonth[$createdMonth])) {
                $countProofsMonth[$createdMonth] = 1;
            } else {
                $countProofsMonth[$createdMonth] ++;
            }

            $createdHour = date('YmdH', $proof->created_date);
            if (!isset($countProofsHour[$createdHour])) {
                $countProofsHour[$createdHour] = 1;
            } else {
                $countProofsHour[$createdHour] ++;
            }

        }

        $today = date('Ymd');
        $todayWeek = date('W');
        $todayMonth = date('m');
        $todayHour = date('YmdH');
        switch ($task->repeat_frequency) {

            case 60*60:
                if (isset($countProofsHour[$todayHour])
                    && $countProofsHour[$todayHour] >= $task->times_frequency) {
                    return false;
                }
                break;

            case 60*60*24:
                if (isset($countProofs[$today])
                    && $countProofs[$today] >= $task->times_frequency) {
                    return false;
                }
                break;
            case 60*60*24*7:
                if (isset($countProofs[$today])
                    && $countProofs[$today] >= 1) {
                    return false;
                }

                if (isset($countProofs[$todayWeek])
                    && $countProofs[$todayWeek] >= $task->times_frequency) {
                    return false;
                }

                break;

            case 60*60*24*7*2 :
                if (isset($countProofs[$today])
                    && $countProofs[$today] >= 1) {
                    return false;
                }

                $countForTwoWeeks = 0;
                if (isset($countProofs[$todayWeek])) {
                    $countForTwoWeeks += $countProofs[$todayWeek];
                }

                if (isset($countProofs[$todayWeek - 1])) {
                    $countForTwoWeeks += $countProofs[$todayWeek - 1];
                }

                if ($countForTwoWeeks >= $task->times_frequency) {
                    return false;
                }

                break;

            case 60*60*24*30:
                if (isset($countProofs[$today])
                    && $countProofs[$today] >= 1) {
                    return false;
                }

                if (isset($countProofs[$todayMonth])
                    && $countProofs[$todayMonth] >= $task->times_frequency) {
                    return false;
                }
                break;
            default:
                if (isset($countProofs[$today])
                    && $countProofs[$today] >= 1) {
                    return false;
                }

        }

        return true;
    }

}
