<?php

namespace backend\modules\fitness\controllers\api;

/**
* This is the class for REST controller "AeExtArticleCategoryController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtArticleCategoryController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\fitness\models\AeExtArticleCategory';
}
