<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtCalendarEntryController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtCalendarEntryController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtCalendarEntry';
}
