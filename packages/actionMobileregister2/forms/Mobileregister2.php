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

$menus = CHtml::listData(Aenavigation::model()->findAllByAttributes(array('app_id' => $this->gid)), 'id', 'name');
$variables = CHtml::listData(Aevariable::model()->findAllByAttributes(array('game_id' => $this->gid)), 'id', 'name');
$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

$defaults = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mobileregister2'), 'hint' => '{%hint_article_action_theme%}'),
);

if(isset($model->config['article_action_theme']) AND $model->config['article_action_theme'] == 'userlist'){
    
    $configs = array(
        'config[userdata]' => array('type'=>'textarea', 'title'=>'%subject%'),
        'config[intro_action]' => array('type'=>'dropdownlist', 'items'=>$actions),
        'config[require_username]' => array('type'=>'checkbox', 'items'=>$actions),
        'config[sender_email]' => array('type'=>'text')
    );

} else {

    $configs = array(
        // Configs
        'config[collect_name]' => array('type'=>'checkbox'),
        'config[show_email]' => array('type'=>'checkbox'),
        'config[strict_email_validation]' => array('type'=>'checkbox'),
        'config[collect_password]' => array('type'=>'checkbox'),
        'config[collect_phone]' => array('type'=>'checkbox'),
        'config[collect_address]' => array('type'=>'checkbox'),

        'config[login_branch]' => array('type'=>'dropdownlist',
            'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        ),

        'config[validate_first_name_only]' => array('type'=>'checkbox','hint' => '{%hint_validate_first_name_only%}'),
        'config[require_login]' => array('type'=>'checkbox','hint' => '{%require_login_after_register%}'),
        'config[require_photo]' => array('type'=>'checkbox','hint' => '{%require_profile_picture%}'),
        'config[ask_for_location]' => array('type'=>'checkbox','hint' => '{%require_location%}'),
        'config[facebook_enabled]' => array('type'=>'checkbox','hint' => '{%require_facebook_enabled%}'),
        'config[require_sex]' => array('type'=>'checkbox','hint' => '{%require_sex%}'),
        'config[require_preferences]' => array('type'=>'checkbox','hint' => '{%require_preferences%}'),
        'config[require_comment]' => array('type'=>'checkbox','hint' => '{%require_comment%}'),
        'config[require_terms]' => array('type'=>'checkbox','hint' => '{%require_terms%}'),
        'config[terms_popup]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%terms_popup%}'),
        'config[require_match_entry]' => array('type'=>'checkbox','hint' => '{%require_match_entry%}'),
        'config[place_popup]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%place_popup%}'),
        'config[tips_action]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%terms_popup%}'),
        'config[simple_social]' => array('type'=>'checkbox','hint' => '{%no_email_password_with_social_logins_hint%}'),
        'config[instagram_login_provider]' => array('type'=>'dropdownlist','items' => CHtml::listData(Aeaction::getAllActions($this->gid),'id','name'), 'hint' => '{%terms_popup%}'),
    );

}

return array_merge( $defaults, $configs );