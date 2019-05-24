<?php

namespace backend\modules\tatjack\controllers\api;

/**
* This is the class for REST controller "AeExtBookingController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtBookingController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tatjack\models\AeExtBooking';
}
