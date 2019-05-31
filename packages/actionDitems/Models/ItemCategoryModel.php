<?php


namespace packages\actionDitems\Models;

use CActiveRecord;

class ItemCategoryModel extends CActiveRecord
{
    public $id;
    public $name;
    public $app_id;
    public $picture;
    public $description;

    // added for relational queries
    public $notes;

    /*
     * for hierarchichal cateogries
     */
    public $parent_id;
    public $image;

    public function tableName()
    {
        return 'ae_ext_items_categories';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'category_relations' => array(self::HAS_MANY, 'packages\actionDitems\Models\ItemCategoryRelationModel', 'category_id', 'joinType'=>'LEFT JOIN')
        );
    }
}