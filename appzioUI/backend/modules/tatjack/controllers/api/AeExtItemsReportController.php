<?php

namespace backend\modules\tatjack\controllers\api;

/**
* This is the class for REST controller "AeExtItemsReportController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtItemsReportController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\tatjack\models\AeExtItemsReport';
}
