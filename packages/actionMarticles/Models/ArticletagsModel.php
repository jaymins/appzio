<?php

namespace packages\actionMarticles\Models;
use CActiveRecord;

class ArticletagsModel extends CActiveRecord {

    public $id;
	public $app_id;
	public $title;

    public function tableName()
    {
        return 'ae_ext_article_tags';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
	    return array(
		    'article' => array(self::BELONGS_TO, 'packages\actionMarticles\Models\ArticlesModel', 'id'),
	    );
    }

}