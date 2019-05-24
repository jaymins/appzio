<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFitProgramExercise as BaseAeExtFitProgramExercise;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_program_exercise".
 */
class AeExtFitProgramExercise extends BaseAeExtFitProgramExercise
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

    public static function addOrUpdateRelations($relations, $ex_id, $check_for_deleted, $sub_type = null)
    {
        if (empty($relations)) {
            return false;
        }

        if (!$sub_type){
            $relations = self::flatten_array($relations);
        }


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
            if (isset($relation['field-select-relation-id']))
            $input_relation_id = $relation['field-select-relation-id'];
            $relation_id = $relation['field-input-id'];

            if (isset($relation_id) AND $relation_id AND $relations_model = AeExtFitProgramExercise::findOne($relation_id)) {
                $current_relations_ids[] = $relation_id;
                $do_update = true;
            } else {
                $relations_model = new AeExtFitProgramExercise();
            }
            $relations_model->program_id = $ex_id;
            $relations_model->time = $relation['field-input-duration'];
            if (isset($relation['field-select-relation-id']))
            $relations_model->exercise_id = $input_relation_id;
            $relations_model->week = ($relation['field-input-week'])?$relation['field-input-week']:0;
            $relations_model->priority = $relation['field-input-priority'];

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
        if ($week) {
            $filter = ['program_id' => $id,
                'week' => $week
            ];
            $order = [
                'week' => SORT_ASC,
                'priority' => SORT_ASC
            ];
        } else {
            $filter = ['program_id' => $id];

            $order = [
                'priority' => SORT_ASC
            ];
        }


        $relations = AeExtFitProgramExercise::find()
            ->where($filter)
            ->orderBy($order)
            ->all();

        if ($relations) {
            return $relations;
        }

        return [];
    }

    private static function flatten_array(array $array)
    {
        $output = array();

        foreach ($array as $item) {
            foreach ($item as $single_item) {
                $output[] = $single_item;
            }
        }

        return $output;
    }

}