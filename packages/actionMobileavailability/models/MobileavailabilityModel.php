<?php

class MobileavailabilityModel extends ArticleModel {


    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobileplaces';
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'aegame' => array(self::BELONGS_TO, 'aegame', 'game_id'),
        );
    }


}