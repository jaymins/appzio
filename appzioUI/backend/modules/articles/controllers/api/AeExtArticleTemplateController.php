<?php

namespace backend\modules\articles\controllers\api;

/**
* This is the class for REST controller "AeExtArticleTemplateController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtArticleTemplateController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\articles\models\AeExtArticleTemplate';
}
