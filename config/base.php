<?php

//    this is the first config that gets included (and thus overriden)
//    include all imports, modules etc. here

$root = dirname(__FILE__).DIRECTORY_SEPARATOR . '..';

return array(
    
    // preloading 'log' component
    'preload'=>array(
        'log',
        'bootstrap'
    ),
    'basePath' => $root,
    'name'=>'ae',
    'language'=>'en',

    // autoloading model and component classes
    'import'=>array(
        'ext.yii-mail.YiiMailMessage',
        'ext.gtc.components.*', // Gii Template Collection,
        'ext.ESES.*',
        'ext.rrule.*',
        'ext.moosend.*',
        'ext.giix-components.*',

        'application.models.*',
        'application.components.*',
        'application.modules.localizeradmin.components.*',
        'application.modules.aeregistration.components.*',
        'application.modules.example.components.*',
        'application.modules.userGroups.components.*',
        'application.modules.userGroups.models.*',
        'application.extensions.modules.acl.components.*',
        'application.extensions.modules.acl.models.*',
        'application.extensions.awegen.components.*',
        'application.extensions.awegen.models.*',
        'system.logging.CLogger',
        // 'application.extensions.yiidebugtb.*', //only for debug!
    ),

    'modules'=>array(
        'userGroups'=>array(
            'accessCode'=>'muumipappa',
            'salt' => '292jasdkasdASDk',
        ),
        'LocalizerAdmin',
        'aeregistration',
        'aegame',
        'aegameauthor',
        'dashboard',
        'aesettings',
        'example',
        'aeplay',
        'aetutorial',
        'aelogic',
        'aedev',
        'aemenu',
        'aescore',
        'aebadge',
        'aelevel',
        'aeapi',
        'aeadmin',
        'aepreview',
        'aemedia',
        'aeiosbuild',
        'aemonitoring'
    ),

    // application components
    'components'=>array(
        'db'=>array(
            'charset' => 'utf8',
            'initSQLs'=>array('SET NAMES "utf8mb4"'),
        ),

    'clientScript'=>array(
            'packages'=>array(
                'jquery'=>array(
                    'baseUrl'=>'https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/',
                    'js'=>array('jquery.min.js'),
                ),
                'jquery.ui'=>array(
                    'baseUrl'=>'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/',
                    'js'=>array('jquery-ui.min.js'),
                ),
                'jquery-css.ui'=>array(
                    'baseUrl'=>'https://ajax.googleapis.com/ajax/libs/jqueryui/',
                    'css' => array('1.11.2/themes/smoothness/jquery-ui.css'),
                ),
            ),
        ),
        'mail' => array(
            'class' => 'ext.yii-mail.YiiMail',
            'transportType' => 'smtp',
            'transportOptions' => array(
                'host' => 'smtp.gmail.com',
                'username' => 'username',
                'password' => 'pass',
                'port' => '465',
                'encryption'=>'ssl',
            ),
            'viewPath' => 'application',
            'logging' => true,
            'dryRun' => false
        ),
        'cache' => array('class' => 'system.caching.CDummyCache'),
        'bootstrap' => array('class' => 'ext.bootstrap.components.Bootstrap',
            'responsiveCss' => true),
        'user' => array(
            'class'=>'userGroups.components.WebUserGroups',
            'allowAutoLogin' => true,
            'autoUpdateFlash' => false, // to make flash messages work also with redirects
        ),
        'aeregistration' => array(
            'class' => 'application.modules.aeregistration.components.Aeregistration'
        ),
        'assetmanager' => array(
            'linkAssets' => true
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                /* invitations setup */
                '<language:[a-z]{2}>/i/<shorturl:\w+>' => 'aeplay/public/invitation',

                /* we use this for images */
                '/img/<width:[0-9]{1,4}>/<height:[0-9]{1,4}>' => '/aeapi/images',

                /* this is the short url for completing an individual task */
                '<language:[a-z]{2}>/a/<token:\w+>' => 'aeplay/home/done',

                /* this is the short url for showing an individual task */
                '<language:[a-z]{2}>/p/<token:\w+>' => 'aeplay/home/showtask',

                /* this is the short url for games public home page */
                '<language:[a-z]{2}>/games/<shortname:\w+>' => 'aeplay/public/findgame',

                /* api setup */
                '/api/<api_key:\w+>/<controller:\w+>/<action:\w+>/' => 'aeapi/<controller>/<action>',

                /* api broken call */
                '/api//<controller:\w+>/<action:\w+>/' => 'aeapi/<controller>/<action>',
                
                /* custom redirector */
                '<language:[a-z]{2}>/open/<appurl:\w+>/' => 'aeplay/public/mobileurl',

                /* app preview */
                '<language:[a-z]{2}>/d/<gid:\w+>/' => 'aepreview/default/index',
                '<language:[a-z]{2}>/d/<gid:\w+>/os/<system:\w+>/' => 'aepreview/default/index',
                '<language:[a-z]{2}>/hash/appid/<appid:\w+>/' => 'aepreview/default/getfbhash',

                /* language setup */
                '<language:[a-z]{2}>/<_m>/<_c>' => '<_m>/<_c>',
                '<language:[a-z]{2}>/<_m>/<_c>/<_a>*' => '<_m>/<_c>/<_a>',
                '<language:[a-z]{2}>/<_m>/<_a>' => '<_m>/<_a>',
                '<language:[a-z]{2}>/<_c>' => '<_c>',
                '<language:[a-z]{2}>/<_c>/<_a>' => '<_c>/<_a>',

                /* usersGroups */
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',

                /* autoadmin */
                '/<module:autoadmin>' => 'autoadmin/default/index',
                '/<module:autoadmin>/<controller:\w+>' => 'autoadmin/<controller>/index',
                '/<module:autoadmin>/<controller:\w+>/<action:\w+>' => 'autoadmin/<controller>/<action>',

                /* temporary fix */
            ),
        ),
        'mustache' => array(
            'class' => 'ext.mustache.components.MustacheApplicationComponent',
            'templatePathAlias' => 'application.templates',
            'templateExtension' => 'mustache',
            'extension' => true,
        ),
        'localizer' => array(
            'class' => 'ext.localizer.components.LocalizerApplicationComponent',
            /* you can test bigger data amounts with http://localizer.nokiantyres.east.fi/api/ */
            'localizerURL' => 'http://localizer.appzio.com/admin/localizer/api/',
            'cacheTime' => '120',
            'extension' => true,
        ),
        'i18n' => array (
            'class' => 'i18n'
        ),
        'MagickMenu' => array(
            'class' => 'MagickMenu'
        ),
        'errorHandler' => array(
            // use 'site/error' action to display errors
            //'errorAction'=>'site/error',
        ),
        'ses' => array(
            'class'=>'ext.ESES.ESES',
            'access_key' => 'AKIAI467NBILFDR2T65A',
            'secret_key' => '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',
            'host' => 'email.eu-west-1.amazonaws.com',
        ),
    ),

    'aliases' => array(
        'moosend' => $root . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR . 'moosend'
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
        // this is used in contact page
        'adminEmail'=>'myemail@domain.com',
        'languages' => array("en" => "In English", "fi" => "Suomeksi"),
        'localizationcategories' => array('menus' => 5, 'ui' => '1'),

        /* clickatell configuration */
        'defaultLanguage' => 'en',
        'smsUser' => '',
        'smsPw' => '',
        'smsApiId' => '',
        'smsUrl' => 'https://api.clickatell.com/',
        'smsFrom' => 'Appzio',

        /* nexmo configuration */
        'smsUrl2' => 'https://rest.nexmo.com/sms/json',
        'smsApiId2' => '',
        'smsPw2' => '',
        'smsFrom2' => 'Appzio',

        'isOffline' => false,		// set to true to develope offline
        'dbName' => 'aeacore',
        'guestUserId' => 3,
        'localizerurl' => 'http://localizer.appzio.com/api/',
        'localizersecret' => '', // this is an api key used by the put-function which inserts missing strings
        'channel_setting_sms' => '7',   // key from table ae_channel_setting for sms channel
        'channel_setting_email' => '1', // key from table ae_channel_setting for email channel
    ),
);