<?php


namespace packages\actionMtasks\Models;
use CActiveRecord;
use packages\actionMtasks\Models\TasksInvitationsModel;

class TasksModel extends CActiveRecord {

    public $id;
    public $owner_id;
    public $assignee_id;
    public $invitation_id;
    public $category_id;
    public $category_name;
    public $created_time;
    public $start_time;
    public $repeat_frequency;
    public $deadline;
    public $title;
    public $description;
    public $picture;
    public $status;
    public $completion;

    public function tableName()
    {
        return 'ae_ext_mtasks';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'invitations' => array(self::BELONGS_TO, 'packages\actionMtasks\Models\TasksInvitationsModel', 'invitation_id'),
        );
    }



}
