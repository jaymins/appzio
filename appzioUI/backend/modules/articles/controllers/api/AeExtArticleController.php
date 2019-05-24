<?php

namespace backend\modules\articles\controllers\api;

/**
* This is the class for REST controller "AeExtArticleController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtArticleController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\articles\models\AeExtArticle';
}
