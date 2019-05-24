<?php

namespace backend\modules\places\controllers\api;

/**
* This is the class for REST controller "AeExtMobileplaceController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtMobileplaceController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\places\models\AeExtMobileplace';
}
