<?php

namespace packages\actionMfood\Models;

use CActiveRecord;

/* this trait includes all model functionality related
to the user's shopping list */


class ShoppingList extends CActiveRecord
{
    public $id;
    public $play_id;
    public $ingredient_id;
    public $date_from;
    public $date_to;
    public $quantity;
    public $gid;

    public function tableName()
    {
        return 'ae_ext_food_shopping_list';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
        ];
    }
}