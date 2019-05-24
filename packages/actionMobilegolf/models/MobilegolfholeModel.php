<?php

class MobilegolfholeModel extends ArticleModel {

    public $play_id;
    public $gid;
    public $event_info;

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_golf_hole';
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'mobileplaces' => array(self::BELONGS_TO, 'mobileplaces', 'place_id'),
            'play' => array(self::BELONGS_TO, 'aeplay', 'play_id'),
        );
    }

    public function getCurrentHole($eventid,$order){

        $results = MobilegolfholeModel::model()->findByAttributes(array('event_id' => $eventid,'order' => $order));

        if(is_object($results)){
            return $results;
        } else {
            return false;
        }

    }
    


}