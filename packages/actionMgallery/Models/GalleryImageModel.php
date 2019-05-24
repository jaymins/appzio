<?php


namespace packages\actionMgallery\Models;

use CActiveRecord;

class GalleryImageModel extends CActiveRecord
{
    public $id;
    public $play_id;
    public $image;
    public $created;

    public function tableName()
    {
        return 'ae_ext_gallery_images';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            //'category_relations' => array(self::HAS_MANY, 'packages\actionMitems\Models\ItemCategoryRelationModel', 'category_id', 'joinType'=>'LEFT JOIN')
        );
    }
}