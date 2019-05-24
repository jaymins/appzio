<?php

namespace packages\actionMarticles\Models;
use CActiveRecord;

class ArticlephotosModel extends CActiveRecord {

    public $id;
    public $article_id;
    public $title;
    public $description;
    public $photo;
    public $position;

    public function tableName()
    {
        return 'ae_ext_article_photos';
    }

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function relations() {
        return array(
            'photos' => array(self::BELONGS_TO, 'packages\actionMarticles\Models\ArticlesModel', 'id'),
        );
    }

}