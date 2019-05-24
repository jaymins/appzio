<?php

namespace backend\modules\articles\controllers\api;

/**
* This is the class for REST controller "AeExtArticleTagController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtArticleTagController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\articles\models\AeExtArticleTag';
}
