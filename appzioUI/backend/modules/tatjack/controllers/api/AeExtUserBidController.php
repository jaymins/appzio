<?php

namespace backend\modules\tatjack\controllers\api;

/**
* This is the class for REST controller "AeExtUserBidController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtUserBidController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tatjack\models\AeExtUserBid';
}
