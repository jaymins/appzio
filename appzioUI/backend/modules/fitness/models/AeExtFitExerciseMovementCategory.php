<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFitExerciseMovementCategory as BaseAeExtFitExerciseMovementCategory;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_exercise_movement_category".
 */
class AeExtFitExerciseMovementCategory extends BaseAeExtFitExerciseMovementCategory
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

        if ($check_for_deleted) {
            $original_relations = self::find()
                ->select('id')
                ->where([
                    'exercise_id' => $ex_id
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

            if ($relation_id AND $relations_model = AeExtFitExerciseMovementCategory::findOne($relation_id)) {
                $current_relations_ids[] = $relation_id;
                $do_update = true;
            } else {
                $relations_model = new AeExtFitExerciseMovementCategory();
            }

            $relations_model->exercise_id = $ex_id;
            if (isset($input_relation_id))
            $relations_model->movement_category = $input_relation_id;
            //$relations_model->timer_type = (isset($relation['field-input-timer']))?$relation['field-input-timer']:'count_down';
            //$relations_model->rounds = $relation['field-input-rounds'];

            if ($do_update) {
                $relations_model->update();
            } else {
                $relations_model->save();
            }

            $count++;
            /*if ($relation['inner-group']){
                AeExtFitExerciseMcategoryMovement::addOrUpdateRelations($relation['inner-group'],$relations_model->id,false);
            }*/

        }

        if ($ids AND $current_relations_ids) {
            $deleted_items = array_diff($ids, $current_relations_ids);

            if ($deleted_items) {
                foreach ($deleted_items as $item_id) {
                    AeExtFitExerciseMovementCategory::deleteAll([
                        'id' => $item_id
                    ]);
                }
            }
        }

        return true;
    }

    public static function getRelationsByID($id)
    {

        $relations = AeExtFitExerciseMovementCategory::find()
            ->where([
                'exercise_id' => $id,
            ])
            ->orderBy([
                'exercise_id' => SORT_ASC,
            ])
            ->all();

        if ($relations) {
            return $relations;
        }

        return [];
    }

}
