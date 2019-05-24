<?php

/*
 * MAIN CONFIGURATION
 * order of applying:
 * 1. base.php (shared between all environments)
 * 2. main.php (this file)
 * 3. servers.php (server specific credentials, paths etc.)
 *
 */

// NOTE: on Docker installation, you normally don't need to change anything here. All edits
// should be done on servers.php file. Only, if you redis is not known by alias "redis"
// would you need to update this file.

return array(

    'development' => array(
        'debug' => true,
        'components' => array(

            'db' => array(
                'enableParamLogging' => false,
                'enableProfiling' => false,
                //'schemaCachingDuration'=>3600,
            ),

            'cache' => array(
                'class' => 'system.caching.CRedisCache',
                'hostname'=>'redis',
                'port'=>6379,
                'database'=>0,
                //'options'=>STREAM_CLIENT_CONNECT,
                //'hashKey' => false,
                //'keyPrefix' => '',
            ),

            "redis" => array(
                "class" => "ext.redis.ARedisConnection",
                "hostname" => "redis",
                "port" => 6379,
                "database" => 1,
                "prefix" => "Yii.redis."
            ),

            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
/*                    array(
                        'class' => 'CPhpMailerLogRoute',
                        'levels' => 'error',
                        'categories' => 'exception.*, system.*',
                        'emails' => array('myemail@domain.com'),
                        'sentFrom' => 'myemail@domain.com',
                        'subject' => 'Error at ae.com',
                    ),*/

                    array(
                        'class'=>'CFileLogRoute', // CWebLogRoute
                        'levels' => 'error',
                        'logFile' => 'application.log',
                        'categories' => 'system.*'
                    ),


                    /*  array(
                          'class' => 'CFileLogRoute',
                          'levels'=>'trace',
                          'logFile' => 'A',
                      ), array(
                          'class' => 'CFileLogRoute',
                          'levels' => 'info',
                          'logFile' => 'B',
                      ), array(
                          'class' => 'CWebLogRoute',
                          'categories' => 'example',
                          'levels' => 'profile',
                          'showInFireBug' => true,
                          'ignoreAjaxInFireBug' => true,
                      ), array(
                          'class' => 'CWebLogRoute',
                          'categories' => 'example',
                      ),*/
                )
            ),
        ),

        'params' => array(
            'commandEnvironment' => 'development',
            'ssl' => false,
        ),

    ),

);
