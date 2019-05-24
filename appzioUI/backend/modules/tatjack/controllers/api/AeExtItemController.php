<?php

namespace backend\modules\tatjack\controllers\api;

/**
* This is the class for REST controller "AeExtItemController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtItemController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tatjack\models\AeExtItem';
}
