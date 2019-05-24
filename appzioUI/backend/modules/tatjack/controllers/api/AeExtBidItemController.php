<?php

namespace backend\modules\tatjack\controllers\api;

/**
* This is the class for REST controller "AeExtBidItemController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtBidItemController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tatjack\models\AeExtBidItem';
}
