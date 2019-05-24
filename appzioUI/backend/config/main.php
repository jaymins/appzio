<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'name' => 'Application Admin',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'enableCsrfValidation' => true,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            // Disable index.php
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
    ],

    // This would make YII read the generated modules
    'modules' => [
        'golf' => [
            'class' => 'backend\modules\golf\Golf',
        ],
        'users' => [
            'class' => 'backend\modules\users\Users',
        ],
        'products' => [
            'class' => 'backend\modules\products\Products',
        ],
        'registration' => [
            'class' => 'backend\modules\registration\Registration',
        ],
        'places' => [
            'class' => 'backend\modules\places\Places',
        ],
        'tatjack' => [
	        'class' => 'backend\modules\tatjack\Tatjack',
        ],
        'articles' => [
            'class' => 'backend\modules\articles\Articles',
        ],
        'tickers' => [
            'class' => 'backend\modules\tickers\Tickers',
        ],
        'quizzes' => [
            'class' => 'backend\modules\quizzes\Quizzes',
        ],
        'items' => [
            'class' => 'backend\modules\items\Items',
        ],
        'fitness' => [
            'class' => 'backend\modules\fitness\Fitness',
        ],
		'gridview' =>  [
			'class' => '\kartik\grid\Module'
			// enter optional module parameters below - only if you need to
			// use your own export download action or custom translation
			// message source
			// 'downloadAction' => 'gridview/export/download',
			// 'i18n' => []
		]
    ],

    'params' => $params,
];