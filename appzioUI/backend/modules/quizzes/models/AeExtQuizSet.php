<?php

namespace backend\modules\quizzes\models;

use backend\modules\quizzes\models\base\AeExtQuizSet as BaseAeExtQuizSet;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_quiz_sets".
 */
class AeExtQuizSet extends BaseAeExtQuizSet
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

    public static function getRelationsByID($quiz_id)
    {
        
        $relations = AeExtQuizSet::find()
            ->where([
                'quiz_id' => $quiz_id
            ])
            ->orderBy([
                'sorting' => SORT_ASC,
                'id' => SORT_ASC
            ])
            ->all();

        if ($relations) {
            return $relations;
        }

        return [];
    }

    public static function addOrUpdateRelations($relations, $app_id, $quiz_id, $check_for_deleted)
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
                    'quiz_id' => $quiz_id
                ])
                ->asArray()
                ->all();

            $ids = array_column($original_relations, 'id');
        }

        foreach ($relations as $relation) {

            $do_update = false;
            $question_id = $relation['field-select-question'];
            $relation_id = $relation['field-input-id'];

            if ($relation_id AND $relations_model = AeExtQuizSet::findOne($relation_id)) {
                $current_relations_ids[] = $relation_id;
                $do_update = true;
            } else {
                $relations_model = new AeExtQuizSet();
            }

            $relations_model->app_id = $app_id;
            $relations_model->quiz_id = $quiz_id;
            $relations_model->question_id = $question_id;
            $relations_model->sorting = $count;

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
                    AeExtQuizSet::deleteAll([
                        'id' => $item_id
                    ]);
                }
            }

        }

        return true;
    }

}