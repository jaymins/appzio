<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFitMovementController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFitMovementController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFitMovement';
}
