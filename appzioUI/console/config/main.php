<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    // 'controllerMap' => [
    //     'fixture' => [
    //         'class' => 'yii\console\controllers\FixtureController',
    //         'namespace' => 'common\fixtures',
    //       ],
    // ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        // Extra translations
        'i18n' => [
            'translations' => [
                'backend*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                ],
            ],
        ],
    ],
    'params' => $params,

    'controllerMap' => [
        // 'fixture' => [ // Fixture generation command line.
        //     'class' => 'yii\console\controllers\FixtureController',
        //     'namespace' => 'common\fixtures',
        // ],
        'custom-batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => false,
            'overwrite' => true,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false,
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            'modelNamespace' => 'backend\\modules\\test\\models',
            'modelQueryNamespace' => 'backend\\modules\\test\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\test\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\test\\search',
            'crudViewPath' => '@backend/modules/test/views',
            'crudPathPrefix' => '/test/',
            // 'templates' =>
            // [
            //     'oemodel' => '@app/modules/crud/crudtemplates'
            // ],
            'crudProviders' => [
                // 'schmunk42\\giiant\\generators\\crud\\providers\\EditorProvider',
                // 'schmunk42\\giiant\\generators\\crud\\providers\\SelectProvider',
                // 'schmunk42\\giiant\\generators\\crud\\providers\\optsProvider',
                'schmunk42\\giiant\\generators\\crud\\providers\\extensions\\DateTimeProvider',
            ],
        ],
        'users-admin' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => false,
            'overwrite' => true,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\users\\models',
            'modelQueryNamespace' => 'backend\\modules\\users\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\users\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\users\\search',
            'crudViewPath' => '@backend/modules/users/views',
            'crudPathPrefix' => '/users/',
        ],
        'golf-batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => false,
            'overwrite' => true,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\golf\\models',
            'modelQueryNamespace' => 'backend\\modules\\golf\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\golf\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\golf\\search',
            'crudViewPath' => '@backend/modules/golf/views',
            'crudPathPrefix' => '/golf/',
        ],
        'products' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => false,
            'overwrite' => true,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\products\\models',
            'modelQueryNamespace' => 'backend\\modules\\products\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\products\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\products\\search',
            'crudViewPath' => '@backend/modules/products/views',
            'crudPathPrefix' => '/products/',
        ],
        'places-batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => false,
            'overwrite' => true,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\places\\models',
            'modelQueryNamespace' => 'backend\\modules\\places\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\places\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\places\\search',
            'crudViewPath' => '@backend/modules/places/views',
            'crudPathPrefix' => '/places/',
        ],
        'tatjack-batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => false,
            'overwrite' => true,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\tatjack\\models',
            'modelQueryNamespace' => 'backend\\modules\\tatjack\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\tatjack\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\tatjack\\search',
            'crudViewPath' => '@backend/modules/tatjack/views',
            'crudPathPrefix' => '/tatjack/',
        ],
        'articles-batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => false,
            'overwrite' => true,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\articles\\models',
            'modelQueryNamespace' => 'backend\\modules\\articles\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\articles\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\articles\\search',
            'crudViewPath' => '@backend/modules/articles/views',
            'crudPathPrefix' => '/articles/',
        ],
        'tickers-batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => false,
            'overwrite' => true,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\tickers\\models',
            'modelQueryNamespace' => 'backend\\modules\\tickers\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\tickers\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\tickers\\search',
            'crudViewPath' => '@backend/modules/tickers/views',
            'crudPathPrefix' => '/tickers/',
        ],
        'quizzes-batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => false,
            'overwrite' => true,
            'enableI18N' => false,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\quizzes\\models',
            'modelQueryNamespace' => 'backend\\modules\\quizzes\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\quizzes\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\quizzes\\search',
            'crudViewPath' => '@backend/modules/quizzes/views',
            'crudPathPrefix' => '/quizzes/',
        ],

        // php yii items-batch --tables=ae_ext_items,ae_ext_items_categories,ae_ext_items_category_item,ae_ext_items_images,ae_game_play,ae_game,usergroups_user,ae_category,usergroups_group
        'items-batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => false,
            'overwrite' => true,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\items\\models',
            'modelQueryNamespace' => 'backend\\modules\\items\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\items\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\items\\search',
            'crudViewPath' => '@backend/modules/items/views',
            'crudPathPrefix' => '/items/',
        ],

        // php yii fitness-batch --tables=ae_ext_fit_program,ae_ext_fit_program_category,ae_ext_fit_exercise,ae_ext_fit_program_exercise,ae_ext_fit_movement,ae_ext_fit_exercise_movement,ae_ext_calendar_entry,ae_ext_calendar_entry_type,ae_ext_article,ae_ext_article_categories,ae_game_play,ae_game,usergroups_user,ae_category,usergroups_group
        'fitness-batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => true,
            'overwrite' => false,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\fitness\\models',
            'modelQueryNamespace' => 'backend\\modules\\fitness\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\fitness\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\fitness\\search',
            'crudViewPath' => '@backend/modules/fitness/views',
            'crudPathPrefix' => '/fitness/',
        ],

        // php yii fitness-batch --tables=ae_ext_purchase,ae_ext_purchase_products
        'purchases-batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => true,
            'overwrite' => true,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\purchases\\models',
            'modelQueryNamespace' => 'backend\\modules\\purchases\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\purchases\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\purchases\\search',
            'crudViewPath' => '@backend/modules/purchases/views',
            'crudPathPrefix' => '/purchases/',
        ],

        'mregister-batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'interactive' => true,
            'overwrite' => false,
            'enableI18N' => true,
            'crudTidyOutput' => true,
            'crudAccessFilter' => false, // no login required for inner pages
            'tablePrefix' => '',
            'modelMessageCategory' => 'backend',
            'crudMessageCategory' => 'backend',

            // Do not edit
            'crudBaseControllerClass' => 'backend\\controllers\\CrudBaseController',
            'modelBaseClass' => 'backend\\models\\CrudBase',
            // Do not edit

            // Do configuration here
            'modelNamespace' => 'backend\\modules\\registration\\models',
            'modelQueryNamespace' => 'backend\\modules\\registration\\models\\query',
            'crudControllerNamespace' => 'backend\\modules\\registration\\controllers',
            'crudSearchModelNamespace' => 'backend\\modules\\registration\\search',
            'crudViewPath' => '@backend/modules/registration/views',
            'crudPathPrefix' => '/registration/',
            'crudSkipRelations' => ['ae_game'],
        ],

    ],

];