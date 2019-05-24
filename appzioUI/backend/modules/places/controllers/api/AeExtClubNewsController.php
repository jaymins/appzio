<?php

namespace backend\modules\places\controllers\api;

/**
* This is the class for REST controller "AeExtClubNewsController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtClubNewsController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\places\models\AeExtClubNews';
}
