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

$buttons=array(
    'no_button' => '{%article_no_button%}',
    'submit' =>  '{%article_standard_complete_button%}',
    'save' =>  '{%article_save_button%}',
);


$providers=array(
    'appzio' => '{%appzio%}',
    'olive' => '{%olive%}'
);

$modes=array(
    'login' => '{%login%}',
    'connector' => '{%connector%}'
);



$arr = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mobileoauth', true), 'onChange' => 'this.form.submit()','hint' => '{%hint_article_action_theme%}'),

    'config[default_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        'hint' => '{%choose_branch_to_open_when_logged_in%}'
    ),

    'config[login_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        'hint' => '{%choose_branch%}'
    ),

    'config[register_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        'hint' => '{%choose_branch%}'
    ),


    'config[logout_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        'hint' => '{%choose_branch%}'
    ),

    'config[register_action]' => array('type'=>'dropdownlist','items' => CHtml::listData(Aeaction::getAllActions($this->gid),'id','name'), 'hint' => '{%terms_popup%}'),
    'config[login_action]' => array('type'=>'dropdownlist','items' => CHtml::listData(Aeaction::getAllActions($this->gid),'id','name'), 'hint' => '{%terms_popup%}'),

    // alert
/*    'config[alertbox]' => array('type'=>'textarea', 'title'=>'%alertbox%'),
    'config[facebook_enabled]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),
    'config[twitter_enabled]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),
    'config[instagram_enabled]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),
    'config[instagram_login_provider]' => array('type'=>'dropdownlist','items' => CHtml::listData(Aeaction::getAllActions($this->gid),'id','name'), 'hint' => '{%terms_popup%}'),*/

);

$webauth = array(
    'config[oauth_action]' => array('type'=>'dropdownlist','items' => CHtml::listData(Aeaction::getAllActions($this->gid),'id','name'), 'hint' => '{%terms_popup%}'),
    'config[oauth_url]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[oauth_client_id]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[oauth_secret]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[oauth_scope]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[oauth_userauth_url]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[oauth_userinfo_url]' => array('type'=>'text', 'title'=>'%subject%'),

);


$arr2 = array(
    'config[button_action]' => array('type'=>'dropdownlist','items' => $buttons, 'hint' => '{%hint_article_button_action%}'),

    'config[mode]' => array('type' => 'dropdownlist',
    'items' => $modes,
    'hint' => '{%choose_mode%}'
),

    'config[provider]' => array('type' => 'dropdownlist',
    'items' => $providers,
    'hint' => '{%choose_provider%}'
),

    'config[endpoint]' => array('type'=>'text', 'title'=>'{%oauth_endpoint_hint%}'),
    'config[link_to_app_download]' => array('type'=>'text', 'title'=>'{%oauth_endpoint_hint%}'),
    'config[test_token]' => array('type'=>'text', 'title'=>'{%oauth_endpoint_hint%}'),
    'config[app_link]' => array('type'=>'text', 'title'=>'{%oauth_applink_hint%}'),
    'config[action_id]' => array('type'=>'text', 'title'=>'{%oauth_action_id_of_other_app_hint%}'),


    'config[terms_popup]' => array('type'=>'dropdownlist','items' => CHtml::listData(Aeaction::getAllActions($this->gid),'id','name'), 'hint' => '{%terms_popup%}'),
    'config[collect_push_permission]' => array('type'=>'checkbox'));

if(isset($model->config['article_action_theme']) AND $model->config['article_action_theme'] == 'olive'){
    return $arr+$webauth;
} else {
    return $arr+$arr2;
}
