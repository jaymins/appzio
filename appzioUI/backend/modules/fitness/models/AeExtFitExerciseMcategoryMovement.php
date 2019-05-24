<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFitExerciseMcategoryMovement as BaseAeExtFitExerciseMcategoryMovement;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_exercise_mcategory_movement".
 */
class AeExtFitExerciseMcategoryMovement extends BaseAeExtFitExerciseMcategoryMovement
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
                    'exercise_movement_cat_id' => $ex_id
                ])
                ->asArray()
                ->all();

            $ids = array_column($original_relations, 'id');
        }

        foreach ($relations as $relation) {

            if (!isset($relation['field-select-sub-relation-id'])) {
                continue;
            }

            $do_update = false;
            $input_relation_id = $relation['field-select-sub-relation-id'];
            $relation_id = $relation['field-input-sub-id'];

            if ($relation_id AND $relations_model = AeExtFitExerciseMcategoryMovement::findOne($relation_id)) {
                $current_relations_ids[] = $relation_id;
                $do_update = true;
            } else {
                $relations_model = new AeExtFitExerciseMcategoryMovement();
            }

            $relations_model->exercise_movement_cat_id = $ex_id;
            $relations_model->weight =  (isset($relation['field-input-sub-weight']))?$relation['field-input-sub-weight']:null;
            $relations_model->unit =    (isset($relation['field-input-sub-unit']))?$relation['field-input-sub-unit']:null;
            $relations_model->reps =    (isset($relation['field-input-sub-reps']))?$relation['field-input-sub-reps']:null;
            $relations_model->rest =    (isset($relation['field-input-sub-rest']))?$relation['field-input-sub-rest']:null;
            $relations_model->movement_time =(isset($relation['field-input-sub-time']))?$relation['field-input-sub-weight']:null;
            $relations_model->movement_id = $input_relation_id;

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
                    AeExtFitExerciseMcategoryMovement::deleteAll([
                        'id' => $item_id
                    ]);
                }
            }
        }

        return true;
    }

    public static function getRelationsByID($id)
    {

        $relations = AeExtFitExerciseMcategoryMovement::find()
            ->where([
                'exercise_movement_cat_id' => $id,
            ])
            ->orderBy([
                'exercise_movement_cat_id' => SORT_ASC,
            ])
            ->all();

        if ($relations) {
            return $relations;
        }

        return [];
    }
}
