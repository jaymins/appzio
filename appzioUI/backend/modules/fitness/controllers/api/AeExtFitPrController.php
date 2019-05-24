<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFitPrController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFitPrController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFitPr';
}
