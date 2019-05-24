<?php

/*

This is the admin form configuration which you can find under admin interface for games.

NOTE: all fields are named config[yourfieldname], nothing else will work. You can invent any
field names, these are available when rendering the view (under views/)

supported field types, uses Yii Booster, this would be your best source of information:
http://www.yiiframework.com/forum/index.php/topic/36258-yiibooster/

'text' => 'textFieldRow',
'password' => 'passwordFieldRow',
'textarea' => 'textAreaRow',
'file' => 'fileFieldRow',
'radio' => 'radioButtonRow',
'checkbox' => 'checkBoxRow',
'listbox' => 'dropDownListRow',
'dropdownlist' => 'dropDownListRow',
'checkboxlist' => 'checkBoxListRow',
'radiolist' => 'radioButtonListRow',

//HTML5 types not supported in YiiBooster yet: render as textField
'url' => 'textFieldRow',
'email' => 'textFieldRow',
'number' => 'textFieldRow',

//'range'=>'activeRangeField', not supported yet
'date' => 'datepickerRow',

//new YiiBooster types
'captcha' => 'captchaRow',
'daterange' => 'dateRangeRow',
'redactor' => 'redactorRow',
'markdowneditor' => 'markdownEditorRow',
'uneditable' => 'uneditableRow',
'radiolistinline' => 'radioButtonListInlineRow',
'checkboxlistinline' => 'checkBoxListInlineRow',
'select2' => 'select2Row'

*/

// Theme based settings
$theme = '';
if ( isset($model->config['article_action_theme']) AND $model->config['article_action_theme'] ) {
    $theme = $model->config['article_action_theme'];
}

$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');
$empty_arr = array( '' => 'N/A' );

$mode = array(
    'matching' => '{%match%}',
    'my_matches' => '{%my_matches%}',
    'my_matches_new_messages' => '{%my_matches_with_new_messages%}',
    'my_invites' => '{%my_invites%}',
    'admin' => '{%admin%}',
    'hide' => '{%hide%}',
    'unmatched' => '{%unmatched%}',
    'unmatchconfirm' => '{%confirm_unmatch%}',
    'hideconfirm' => '{%confirm_hide%}',
    'blockconfirm' => '{%confirm_block%}',
    'blocked' => '{%blocked%}'
);

if ( $theme == 'rantevu' ) {
    $mode['payments'] = '{%payments_popup%}';
}

$arr = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mobilematching'), 'hint' => '{%hint_article_action_theme%}'),
    'config[chat_content]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[user_images]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[pull_to_refresh]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),

    'config[mode]' => array('type'=>'dropdownlist','items' => $mode, 'hint' => '{%action_mode%}','onChange'=>'this.form.submit()'),

    // Configs
    'config[show_email]' => array('type'=>'checkbox'),
    'config[detail_view]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_detail_view%}'),
    'config[details_action_id]' => array('type'=>'dropdownlist','items' => array_replace_recursive( $empty_arr, $actions ), 'hint' => '{%hint_details_action_id%}'),
    'config[chat]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_chat%}'),
    'config[invite_action]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_invite_action%}'),
    'config[push_permission]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_push_permission%}'),
    'config[main_view]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%main_main_view%}'),
    'config[intro_action]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_invite_action%}'),
    'config[intro_repeats]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[intro_delay]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[register_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        'hint' => '{%choose_branch%}'
    ),

    'config[google_adcode_banners]' => array('type'=>'text', 'title'=>'{%google_ad_code_banners%}'),
    'config[monitor_region]' => array('type'=>'text', 'title'=>'{%monitor_region_hint%}'),

    'config[user_can_disable_ads]' => array('type'=>'checkbox','hint' => '{%hint_user_can_disable_ads%}'),

    'config[show_interstitials]' => array('type'=>'checkbox','hint' => '{%hint_show_interstitials%}'),
    'config[use_false_id]' => array('type'=>'checkbox','hint' => '{%hint_show_interstitials%}'),
    'config[use_miles]' => array('type'=>'checkbox','hint' => '{%hint_use_miles%}'),
    'config[share_url]' => array('type'=>'text'),
    'config[share_description]' => array('type'=>'text'),
    'config[min_user_dimentions]' => array('type'=>'text'),
);


if(isset($model->config['mode']) AND $model->config['mode'] == 'my_matches'){

    $arr2 = array(
        'config[mymatches_search_withscreenname]' => array('type'=>'checkbox','hint' => '{%hint_mymatches_search_withscreenname%}'),
        'config[mymatches_show_incoming_requests]' => array('type'=>'checkbox','hint' => '{%hint_mymatches_show_incoming_requests%}'),
        'config[mymatches_show_outgoing_requests]' => array('type'=>'checkbox','hint' => '{%hint_mymatches_show_outgoing_requests%}'),
        'config[mymatches_show_group_chats]' => array('type'=>'checkbox','hint' => '{%hint_mymatches_show_group_chats%}'),
    );
    
    $arr = $arr+$arr2;
}

switch ( $theme ) {
    case 'golf':
        
        $arr2 = array(
            'config[action_id_golfizzmain]' => array('type'=>'text','hint' => '{%hint_action_id_golfizzmain%}'),
        );

        $arr = $arr+$arr2;

        break;
    
    case 'olive':
        
        $arr2 = array(
            'config[my_matches_invites]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_my_matches_invites%}'),
            'config[my_matches_messages]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_my_matches_messages%}'),
            'config[action_id_groupchats]' => array('type'=>'dropdownlist','items' => $actions,'hint' => '{%hint_action_id_groupchats%}'),
            'config[action_id_categorysearch]' => array('type'=>'dropdownlist','items' => $actions,'hint' => '{%hint_action_id_groupchats%}'),
        );

        $arr = $arr+$arr2;

        break;

    case 'desee':
        
        $arr2 = array(
            'config[total_swipes_per_day]' => array('type'=>'text', 'hint' => '{%hint_total_swipes_per_day%}'),
        );

        $arr = $arr+$arr2;

        break;
}

return $arr;