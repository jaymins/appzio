<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtFitProgramController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtFitProgramController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtFitProgram';
}
