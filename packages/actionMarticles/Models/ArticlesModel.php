<?php

namespace packages\actionMarticles\Models;

use CActiveRecord;

class ArticlesModel extends CActiveRecord
{

    public $id;
    public $app_id;
    public $category_id;
    public $play_id;
    public $title;
    public $header;
    public $content;
    public $link;
    public $rating;
    public $featured;
    public $article_date;

    public function tableName()
    {
        return 'ae_ext_article';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'photos' => array(self::HAS_MANY, 'packages\actionMarticles\Models\ArticlephotosModel', 'article_id'),
            'categories' => array(self::BELONGS_TO, 'packages\actionMarticles\Models\ArticlecategoriesModel', 'category_id'),
        );
    }

}