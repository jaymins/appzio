<?php
/*
 * MAIN CONFIGURATION
 * order of applying:
 * 1. base.php (shared between all environments)
 * 2. main.php (this file)
 * 3. servers.php
 *
 */

return
    array (
        // IMPORTANT: put your network ip address here
        '127.0.0.1' =>
            array (
                'alias' => 'app.appzio.com',
            ),
		'192.168.43.38' =>
            array (
                'alias' => 'app.appzio.com',
            ),
        '192.168.1.105' =>
            array (
                'alias' => 'app.appzio.com',
            ),
        'app.appzio.com' =>
            array (
                'environment' => 'development',
                'components' =>
                    array (
                        'db' =>
                            array (
                                // change here if you are not using the mariadb dockcer container
                                'connectionString' => 'mysql:host=appziodb;dbname=appziodb',
                                'username' => 'appziouser',
                                'password' => 'appziopwd',
                            ),
                        'cache' =>
                            array (
                                'database' => 7,
                            ),
                    ),
                'params' =>
                    array (
                        // IMPORTANT: replace this with the license key from dashboard.appzio.com
                        'license_key' => 'c02eef2df9fdf18d9048061f60d6f49b5e94f4eea0e0f',
                        'siteURLssl' => 'http://app.appzio.com',
                        'manage_api_enabled' => true,
                        'ssl' => false,
                        'imageMagickPath' => '/usr/bin/',
                        'lockDirPath' => '/tmp/aelogic-lock-fiaengine',
                        'phpExePath' => '/usr/bin/php',
                        'logicScriptPath' => '/var/www/app.appzio.com/aecore/app/protected/yiic.php',
                        'nicePath' => '/bin/nice -n 19',
                        'sleep' => 1000000,
                        'sleep_less' => 100000,
                        'fbAe' => '153577984828716',
                        'fbAeSecret' => '9a3258a41d38f7493bc0e51b9e0d642a',
                    ),
            ),
    );
