<?php

class MobileeventparticipantsModel extends ArticleModel {


    public $play_id;
    public $event_id;
    public $status;

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobileevents_participants';
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'mobilevents' => array(self::BELONGS_TO, 'mobilevents', 'event_id'),
        );
    }

}