<?php

// $params = (include __DIR__ . '/../../backend/config/params.php');
// $location = str_replace('http://', '', $path);

$location = ( isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'ae.com') );
$configs = (include __DIR__ . '/../../../../app/protected/config/servers.php');

if ( $location == 'localhost' OR (filter_var($location, FILTER_VALIDATE_IP) !== false) ) {
    $location = 'ae.com';
}

if ( !isset($configs[$location]) ) {
    die( 'Missing configuration .. ' );
}

$config = $configs[$location];
$db_config = $config['components']['db'];

$dsn = $db_config['connectionString'];
$username = $db_config['username'];
$password = $db_config['password'];

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => $dsn,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];