<?php

namespace backend\modules\tasks\controllers\api;

/**
* This is the class for REST controller "AeExtMtasksInvitationController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtMtasksInvitationController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tasks\models\AeExtMtasksInvitation';
}
