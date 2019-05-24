<?php

namespace backend\modules\fitness\models;

use Yii;
use \backend\modules\fitness\models\base\AeExtFitComponent as BaseAeExtFitComponent;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_fit_component".
 */
class AeExtFitComponent extends BaseAeExtFitComponent
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
    public static function getRelationsByID($component_id)
    {

        $relations = AeExtFitComponentMovement::find()
            ->where([
                'component_id' => $component_id
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
