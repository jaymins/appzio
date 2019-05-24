<?php


/* this is used to transfer "pending" data to the correct play */

namespace packages\actionMregister\themes\stepbystep\Models;

use packages\actionMnotifications\Models\NotificationsModel;

Trait TransferData {

    public function transferData(){

        $sql1 = "UPDATE ae_ext_notifications SET play_id_to = :playID, temporary_email = '' WHERE temporary_email = :email";
        \Yii::app()->db
            ->createCommand($sql1)
            ->bindValues(array(
                ':email' => $this->getSavedVariable('email'),
                ':playID' => $this->playid))
            ->query();

        $sql2 = "UPDATE ae_ext_mtasks 
                LEFT JOIN ae_ext_mtasks_invitations ON ae_ext_mtasks.invitation_id = ae_ext_mtasks_invitations.id
                SET ae_ext_mtasks.assignee_id = :playID
                WHERE ae_ext_mtasks_invitations.email = :email 
                  AND (ae_ext_mtasks_invitations.invited_play_id IS NULL OR ae_ext_mtasks_invitations.invited_play_id = '')
                  AND (ae_ext_mtasks.assignee_id IS NULL OR ae_ext_mtasks.assignee_id = '')
                  ";
        \Yii::app()->db
            ->createCommand($sql2)
            ->bindValues(array(
                ':email' => $this->getSavedVariable('email'),
                ':playID' => $this->playid))
            ->query();

        $sql3 = "UPDATE ae_ext_mtasks_invitations SET invited_play_id = :playID WHERE email = :email AND invited_play_id IS NULL OR invited_play_id = ''";
        \Yii::app()->db
            ->createCommand($sql3)
            ->bindValues(array(
                ':email' => $this->getSavedVariable('email'),
                ':playID' => $this->playid))
            ->query();


    }



}