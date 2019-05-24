<?php

namespace backend\modules\fitness\models;

use backend\modules\fitness\models\base\AeExtFitProgramRecipe as BaseAeExtFitProgramRecipe;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_program_recipe".
 */
class AeExtFitProgramRecipe extends BaseAeExtFitProgramRecipe
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

        $relations = self::flatten_array($relations);

        $count = 0;
        $ids = [];
        $current_relations_ids = [];

        if ($check_for_deleted) {
            $original_relations = self::find()
                ->select('id')
                ->where([
                    'program_id' => $ex_id
                ])
                ->asArray()
                ->all();

            $ids = array_column($original_relations, 'id');
        }

        foreach ($relations as $relation) {
            $do_update = false;
            $exercise_id = $relation['field-select-relation-id'];
            $relation_id = $relation['field-input-id'];

            if ($relation_id AND $relations_model = AeExtFitProgramRecipe::findOne($relation_id)) {
                $current_relations_ids[] = $relation_id;
                $do_update = true;
            } else {
                $relations_model = new AeExtFitProgramRecipe();
            }

            $relations_model->program_id = $ex_id;
            $relations_model->recipe_id = $exercise_id;
            $relations_model->week = $relation['field-input-week'];
            $relations_model->recipe_order = $relation['field-input-priority'];

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
                    AeExtFitProgramExercise::deleteAll([
                        'id' => $item_id
                    ]);
                }
            }
        }

        return true;
    }

    public static function getRelationsByID($id, $week)
    {

        $relations = AeExtFitProgramRecipe::find()
            ->where([
                'program_id' => $id,
                'week' => $week
            ])
            ->orderBy([
                'week' => SORT_ASC,
                'recipe_order' => SORT_ASC
            ])
            ->all();

        if ($relations) {
            return $relations;
        }

        return [];
    }

    private static function flatten_array(array $array) {
        $output = array();

        foreach ($array as $item) {
            foreach ($item as $single_item) {
                $output[] = $single_item;
            }
        }

        return $output;
    }

}