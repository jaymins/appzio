<?php

namespace backend\modules\items\models;

use backend\modules\items\models\base\AeExtItemsCategoryItem as BaseAeExtItemsCategoryItem;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_items_category_item".
 */
class AeExtItemsCategoryItem extends BaseAeExtItemsCategoryItem
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

    public static function addOrUpdateRelations(array $categories, int $item_id, $check_for_deleted)
    {

        if (empty($categories)) {
            return false;
        }

        $count = 0;
        $ids = [];
        $current_relations_ids = [];

        if ($check_for_deleted) {
            $original_relations = self::find()
                ->select('id')
                ->where([
                    'item_id' => $item_id
                ])
                ->asArray()
                ->all();

            $ids = array_column($original_relations, 'id');
        }

        foreach ($categories as $category) {

            $do_update = false;
            $category_id = $category['field-select-category'];
            $relation_id = $category['field-input-id'];

            if ($relation_id AND $relations_model = self::findOne($relation_id)) {
                $current_relations_ids[] = $relation_id;
                $do_update = true;
            } else {
                $relations_model = new self();
            }

            $relations_model->item_id = $item_id;
            $relations_model->category_id = $category_id;

            if ($do_update) {
                $relations_model->update();
            } else {
                $relations_model->save();
            }

            $count++;
        }

        if ($ids AND $current_relations_ids) {
            $deleted_items = array_diff($ids, $current_relations_ids);

            if ($deleted_items) {
                foreach ($deleted_items as $deleted_item_id) {
                    self::deleteAll([
                        'id' => $deleted_item_id
                    ]);
                }
            }
        }

        return true;
    }

}