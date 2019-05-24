<?php

$name_modes = array(
    'default' => '{%full_name%}',
    'first_name' =>  '{%first_name_only%}',
    'last_name' =>  '{%last_name_only%}',
    'hidden' =>  '{%hidden_entirely%}',
);

$menus = CHtml::listData(Aenavigation::model()->findAllByAttributes(array('app_id' => $this->gid)), 'id', 'name');
$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

$mode = array(
    'chat' => '{%individual_chat%}',
    'listing' =>  '{%chats_listing%}',
);

return array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[mode]' => array('type'=>'dropdownlist','items' => $mode, 'hint' => '{%action_mode%}'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mmessaging'), 'hint' => '{%hint_article_action_theme%}'),
    'config[name_mode]' => array('type'=>'dropdownlist','items' => $name_modes, 'hint' => '{%action_mode%}'),
    'config[disable_header]' => array('type'=>'checkbox', 'hint' => '{%hint_disable_header%}'),
    'config[use_server_time]' => array('type'=>'checkbox', 'hint' => '{%hint_use_server_time%}'),
);