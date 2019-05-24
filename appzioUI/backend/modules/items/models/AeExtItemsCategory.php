<?php

namespace backend\modules\items\models;

use backend\modules\items\models\base\AeExtItemsCategory as BaseAeExtItemsCategory;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_items_categories".
 */
class AeExtItemsCategory extends BaseAeExtItemsCategory
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    public static function getAllCategories(int $app_id)
    {
        
        $categories = self::find()
            ->where([
                'app_id' => $app_id
            ])
            ->asArray() // optional
            ->all();

        if ($categories) {
            return $categories;
        }

        return [];
    }

}