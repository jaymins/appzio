<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFitComponentMovement as BaseAeExtFitComponentMovement;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_component_movement".
 */
class AeExtFitComponentMovement extends BaseAeExtFitComponentMovement
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

        $count = 1;
        $ids = [];
        $current_relations_ids = [];

        if ($check_for_deleted) {
            $original_relations = self::find()
                ->select('id')
                ->where([
                    'component_id' => $ex_id
                ])
                ->asArray()
                ->all();

            $ids = array_column($original_relations, 'id');
        }

        foreach ($relations as $order=>$relation) {
            
           if (!isset($relation['field-select-relation-id'])) {
                continue;
            }

            $do_update = false;
            $input_relation_id = $relation['field-select-relation-id'];
            $relation_id = $relation['field-input-id'];

            if ($relation_id AND $relations_model = AeExtFitComponentMovement::findOne($relation_id)) {
                $current_relations_ids[] = $relation_id;
                $do_update = true;
            } else {
                $relations_model = new AeExtFitComponentMovement();
            }

            $relations_model->component_id = $ex_id;
            $relations_model->movement_id = $input_relation_id;
            $relations_model->reps = (isset($relation['field-select-reps']))?$relation['field-select-reps']:null;
            $relations_model->weight = (isset($relation['field-select-weight']))?$relation['field-select-weight']:null;
            $relations_model->unit = (isset($relation['field-select-unit']))?$relation['field-select-unit']:null;
            $relations_model->movement_time = (isset($relation['field-select-movement_time']))?$relation['field-select-movement_time']:null;
            $relations_model->pr_id = (isset($relation['field-select-pr'])&&$relation['field-select-unit']=='%')?$relation['field-select-pr']:null;
            $relations_model->sorting =  $count++;

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
                    AeExtFitComponentMovement::deleteAll([
                        'id' => $item_id
                    ]);
                }
            }
        }

        return true;
    }
}
