<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFitExercise as BaseAeExtFitExercise;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_exercise".
 */
class AeExtFitExercise extends BaseAeExtFitExercise
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
    public static function getAllMovements()
    {
        $recipes = AeExtFitMovement::find()
            ->asArray()// optional
            ->all();

        if ($recipes) {
            return $recipes;
        }

        return [];
    }
    public static function getAllComponents()
    {
        $recipes = AeExtFitComponent::find()
            ->asArray()// optional
            ->all();

        if ($recipes) {
            return $recipes;
        }

        return [];
    }
    public static function getRelationsByID($exercise_id)
    {

        $relations = AeExtFitExerciseComponent::find()
            ->where([
                'exercise_id' => $exercise_id
            ])
            ->orderBy([
                'sorting' => SORT_ASC
            ])
            ->all();

        if ($relations) {
            return $relations;
        }

        return [];
    }
}
