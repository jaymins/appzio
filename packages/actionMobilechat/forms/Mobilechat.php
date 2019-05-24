<?php

$buttons=array(
    'no_button' => '{%article_no_button%}',
    'submit' =>  '{%article_standard_complete_button%}',
    'save' =>  '{%article_save_button%}',
);

$name_modes=array(
    'default' => '{%full_name%}',
    'first_name' =>  '{%first_name_only%}',
    'nickname' =>  '{%nickname%}',
    'last_name' =>  '{%last_name_only%}',
    'hidden' =>  '{%hidden_entirely%}',
    'company' => '{%company%}'
);

$menus = CHtml::listData(Aenavigation::model()->findAllByAttributes(array('app_id' => $this->gid)), 'id', 'name');
$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

$mode=array(
    'individual_chat' => '{%individual_chat%}',
    'group_chat_listing' =>  '{%group_chat_listing%}',
);

$arr = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[mode]' => array('type'=>'dropdownlist','items' => $mode, 'hint' => '{%action_mode%}','onChange'=>'this.form.submit()'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mobilechat', true), 'hint' => '{%hint_article_action_theme%}'),
    'config[keep_scroll_in_bottom]' => array('type'=>'checkbox', 'hint' => '{%menu_for_submit_button%}'),

    'config[profile_action_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_profile_action_id%}'),
    'config[use_referrer]' => array('type'=>'checkbox', 'hint' => '{%hint_save_to_variable%}'),
    'config[use_false_id]' => array('type'=>'checkbox', 'hint' => '{%hint_save_to_variable%}'),
    'config[disable_header]' => array('type'=>'checkbox', 'hint' => '{%hint_save_to_variable%}'),
    'config[use_server_time]' => array('type'=>'checkbox', 'hint' => '{%hint_use_server_time%}'),

    'config[detail_view]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_profile_action_id%}'),
);

if(isset($model->config['mode']) AND $model->config['mode'] == 'group_chat_listing'){

    $arr2 = array(
        'config[chat]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_profile_action_id%}'),
    );
    
} else {

    $arr2 = array(
        'config[name_mode]' => array('type'=>'dropdownlist','items' => $name_modes, 'hint' => '{%action_mode%}','onChange'=>'this.form.submit()'),
        'config[save_match_when_chatting]' => array('type'=>'checkbox', 'hint' => '{%save_match_when_chatting%}'),
        'config[strip_phonenumbers]' => array('type'=>'checkbox', 'hint' => '{%hint_save_to_variable%}'),
        'config[strip_urls]' => array('type'=>'checkbox', 'hint' => '{%hint_save_to_variable%}'),
        'config[pic_permission]' => array('type'=>'checkbox', 'hint' => '{%hint_save_to_variable%}'),
        'config[hide_time]' => array('type'=>'checkbox', 'hint' => '{%hint_hide_time%}'),
        'config[limit_monologue]' => array('type'=>'text', 'title'=>'{%define_the_number_of_monologue_messages_allowed%}'),
        'config[can_invite_others]' => array('type'=>'checkbox', 'hint' => '{%hint_hide_time%}'),
        'config[context_chat_id]' => array('type'=>'checkbox', 'hint' => '{%hint_context_chat_id%}'),
    );

}

if(isset($model->config['article_action_theme']) AND $model->config['article_action_theme'] == 'golf'){
    $arr4 = array('config[action_id_golfizzmain]' => array('type'=>'text','hint' => '{%action_id_on_golfizz_for_new_event%}'));
    $arr = $arr+$arr4;
}

$arr = $arr+$arr2;

return $arr;