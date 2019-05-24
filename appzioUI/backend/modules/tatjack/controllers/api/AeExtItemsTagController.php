<?php

namespace backend\modules\tatjack\controllers\api;

/**
* This is the class for REST controller "AeExtItemsTagController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtItemsTagController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tatjack\models\AeExtItemsTag';
}
