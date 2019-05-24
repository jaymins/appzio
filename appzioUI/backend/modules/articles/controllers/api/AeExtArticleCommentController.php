<?php

namespace backend\modules\articles\controllers\api;

/**
* This is the class for REST controller "AeExtArticleCommentController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AeExtArticleCommentController extends \yii\rest\ActiveController
{
public $modelClass = 'backend\modules\articles\models\AeExtArticleComment';
}
