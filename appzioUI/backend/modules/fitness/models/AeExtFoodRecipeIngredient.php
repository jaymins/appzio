<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFoodRecipeIngredient as BaseAeExtFoodRecipeIngredient;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_food_recipe_ingredient".
 */
class AeExtFoodRecipeIngredient extends BaseAeExtFoodRecipeIngredient
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
    public static function addOrUpdateRelations($relations, $ex_id, $check_for_deleted)
    {

        if (empty($relations)) {
            return false;
        }
        $count = 0;
        $ids = [];
        $current_relations_ids = [];

        if ( $check_for_deleted ) {
            $original_relations = self::find()
                ->select('id')
                ->where([
                    'recipe_id' => $ex_id
                ])
                ->asArray()
                ->all();

            $ids = array_column($original_relations, 'id');
        }

        foreach ($relations as $relation) {
            $do_update = false;
            $ingredient_id = $relation['field-select-ingredient'];
            $relation_id = $relation['field-input-id'];

            if ($relation_id AND $relations_model = AeExtFoodRecipeIngredient::findOne($relation_id)) {
                $current_relations_ids[] = $relation_id;
                $do_update = true;
            } else {
                $relations_model = new AeExtFoodRecipeIngredient();
            }


            $relations_model->recipe_id = $ex_id;
            $relations_model->ingredient_id = $ingredient_id;
            $relations_model->quantity = $relation['field-input-quantity'];

            if ($do_update) {
                $relations_model->update();
            } else {
                $relations_model->save();
            }
            $count++;
        }
        if ( $ids AND $current_relations_ids ) {
            $deleted_items = array_diff($ids, $current_relations_ids);

            if ( $deleted_items ) {
                foreach ($deleted_items as $item_id) {
                    AeExtFoodRecipeIngredient::deleteAll([
                        'id' => $item_id
                    ]);
                }
            }

        }

        return true;
    }
    public static function getRelationsByID($exercise_id)
    {

        $relations = AeExtFoodRecipeIngredient::find()
            ->where([
                'recipe_id' => $exercise_id
            ])
            ->orderBy([
                'id' => SORT_ASC
            ])
            ->all();

        if ($relations) {
            return $relations;
        }

        return [];
    }

}
