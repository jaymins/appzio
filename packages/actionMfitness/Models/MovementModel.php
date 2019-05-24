<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class MovementModel extends CActiveRecord
{

    public $id;
    public $app_id;
    public $article_id;
    public $name;
    public $description;
    public $video_url;

    public function tableName()
    {
        return 'ae_ext_fit_movement';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            //'movement_meta' => array(self::BELONGS_TO,'packages\actionMfitness\Models\ComponentMovementModel',['movement_id' => 'id'])
           /* 'article' => array(self::BELONGS_TO, 'packages\actionMarticles\Models\ArticlesModel', [
                'article_id' => 'id'
            ]),*/
        );
    }

}