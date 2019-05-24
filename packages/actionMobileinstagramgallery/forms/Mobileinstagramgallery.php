<?php

Yii::import('application.modules.aelogic.article.components.*');

$menus = CHtml::listData(Aenavigation::model()->findAllByAttributes(array('app_id' => $this->gid)), 'id', 'name');
$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

$instagram = new InstagramConnector(CLIENT_ID, CLIENT_SECRET, null);

$base_url = Yii::app()->getBaseUrl(true);
$url = Yii::app()->request->requestUri;

$url = URLHelpers::remove_query_arg( 'code', $url );
$url = str_replace('?', '/', $url);
$url = str_replace(array('&', '='), array( '/', '/' ), $url);

$return_url = $base_url . $url;

$loginUrl = $instagram->authorizeUrl(REDIRECT_URI . '?return_uri=' . htmlentities($return_url));

return array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[gallery_item_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_article_button_action%}'),

    'config[instagram_access_token]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[instagram_username]' => array('type'=>'text', 'title'=>'%username%'),
    'config[instagram_bio]' => array('type'=>'text', 'title'=>'%bio%'),
    'config[instagram_website]' => array('type'=>'text', 'title'=>'%website%'),
    'config[instagram_profile_img]' => array('type'=>'text', 'title'=>'%profile_picture%'),
    'config[instagram_full_name]' => array('type'=>'text', 'title'=>'%full_name%'),
    'config[instagram_posts_count]' => array('type'=>'text', 'title'=>'%posts_count%'),
    'config[instagram_followed_by]' => array('type'=>'text', 'title'=>'%followed_by%'),
    'config[instagram_follows]' => array('type'=>'text', 'title'=>'%follows%'),
    'config[instagram_id]' => array('type'=>'text', 'title'=>'%id%'),

    'config[instagramdata]' => array('type'=>'textarea', 'title'=>'%instagramdata%'),

    'config[instagram_connect]' => array(
        'type'  => 'text',
        'title' => '%instagram_connect%',
        'class' => 'instagram-connect',
        'hint'  => '<a class="btn btn-mini btn-primary" href="'. $loginUrl .'">Connect to Instagram</a> <a class="btn btn-mini btn-primary" href="'. URLHelpers::add_query_arg( 'flush-instagram-images', 1 ) .'">Flush Data</a>'
    ),
);