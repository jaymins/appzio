<?php

namespace backend\modules\fitness\models;

use backend\modules\fitness\models\base\AeExtFoodRecipeStep as BaseAeExtFoodRecipeStep;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_food_recipe_step".
 */
class AeExtFoodRecipeStep extends BaseAeExtFoodRecipeStep
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

    public static function getRelationsByID($recipe_id)
    {

        $relations = AeExtFoodRecipeStep::find()
            ->where([
                'recipe_id' => $recipe_id
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

    public static function addOrUpdateRelations($relations, $ex_id, $check_for_deleted)
    {

        if (empty($relations)) {
            return false;
        }

        $count = 0;
        $ids = [];
        $current_relations_ids = [];

        if ($check_for_deleted) {
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
            $relation_id = $relation['field-input-id'];

            if ($relation_id AND $relations_model = AeExtFoodRecipeStep::findOne($relation_id)) {
                $current_relations_ids[] = $relation_id;
                $do_update = true;
            } else {
                $relations_model = new AeExtFoodRecipeStep();
            }


            $relations_model->recipe_id = $ex_id;
            $relations_model->description = $relation['field-input-step'];
            $relations_model->time = $relation['field-input-time'];
            // $relations_model->step_id = $step_id;

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
                foreach ($deleted_items as $item_id) {
                    AeExtFoodRecipeStep::deleteAll([
                        'id' => $item_id
                    ]);
                }
            }

        }

        return true;
    }

}