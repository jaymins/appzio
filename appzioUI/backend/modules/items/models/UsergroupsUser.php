<?php

namespace backend\modules\items\models;

use Yii;
use \backend\modules\items\models\base\UsergroupsUser as BaseUsergroupsUser;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "usergroups_user".
 */
class UsergroupsUser extends BaseUsergroupsUser
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
}
