<?php


namespace packages\actionMtasks\Models;
use CActiveRecord;

class TasksInvitationsModel extends CActiveRecord {

    public $id;
    public $play_id;
    public $invited_play_id;
    public $email;
    public $name;
    public $nickname;
    public $primary_contact;
    public $status;

    /* these are not in the db, but can be enriched */
    public $profilepic;
    public $username;
    public $deals;

    public function tableName()
    {
        return 'ae_ext_mtasks_invitations';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'product' => array(self::BELONGS_TO, 'ProductitemsModel', 'product_id'),
        );
    }



}
