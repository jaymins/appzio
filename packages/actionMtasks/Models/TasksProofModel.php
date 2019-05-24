<?php


namespace packages\actionMtasks\Models;
use CActiveRecord;
use packages\actionMtasks\Models\TasksModel;

class TasksProofModel extends CActiveRecord {

    public $id;
    public $task_id;
    public $created_date;
    public $description;
    public $comment;
    public $status;
    public $photo;

    /* these are not in the db, but can be enriched */
    public $profilepic;
    public $username;

    public function tableName()
    {
        return 'ae_ext_mtasks_proof';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'task' => array(self::BELONGS_TO, 'packages\actionMtasks\Models\TasksModel', 'task_id'),
        );
    }



}
