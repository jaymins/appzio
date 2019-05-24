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

$static = array(
    'create' => array('id' => 'createVar', 'name' => '{%create_variable%}'));

$array =  array(
    'config[subject]' => array('type'=>'text', 'hint' => '{%action_msg_subject%}'),
    'config[msg]' => array('type'=>'redactor', 'hint' => '{%action_msg_body%}'),

    'config[subject_tofriend]' => array('type'=>'text', 'hint' => '{%action_msg_subject_tofriend%}'),
    'config[msg_tofriend]' => array('type'=>'redactor', 'hint' => '{%action_msg_body_tofriend%}'),

    'config[shortmsg]' => array('type'=>'textarea','rows'=>5, 'width' => '100%', 'maxlength' => 200, 'hint' => '{%shortmsg_hint%}'),
    'config[shortmsg_tofriend]' => array('type'=>'textarea','rows'=>5, 'width' => '100%', 'maxlength' => 200, 'hint' => '{%shortmsg_hint%}'),

    'config[fieldtype1]' => array('type'=>'hidden', 'value' => 'textarea'),
    'config[fieldtype2]' => array('type'=>'hidden', 'value' => 'textarea'),

    'config[fieldtitle1]' => array('type'=>'hidden', 'value' => '{%email_addresses%}'),
    'config[fieldtitle2]' => array('type'=>'hidden', 'value' => '{%custom_message%}'),

    'config[fieldhint1]' => array('type'=>'hidden', 'value' => '{%emailfriends_email_addresses_hint%}'),
    'config[fieldhint2]' => array('type'=>'hidden', 'value' => '{%emailfriends_custom_message_hint%}'),

    'config[friendthanks]' => array('type'=>'redactor', 'value' => '{%emailfriends_thank_you_friend%}', 'hint' => '{%emailfriends_thankfriend_hint%}'),


    'config[variable1]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData($static + Aegame::getVariables($this->gid),'id','name'),
        'hint' => '{%variable_collect_emails%}'
    ),

    'config[variable2]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData($static + Aegame::getVariables($this->gid),'id','name'),
        'hint' => '{%variable_collect_msg%}'
    ),

    'config[average_save]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData($static + Aegame::getVariables($this->gid),'id','name'),
        'hint' => '{%variable_collect_average%}'
    ),

);

$array['config[variable1]'] = HtmlHelpers::VarField($this->gid,'{%variable_collect_emails%}','variable1','varediting1');
$array['config[variable2]'] = HtmlHelpers::VarField($this->gid,'{%variable_collect_msg%}','variable2','varediting2');

$array['config[mode]'] = array('type' => 'dropdownlist',
    'items' => array('id' => '{%collect_average%}'),
    //  'hint' => '{%variable_collect_emails%}'
);

$array['config[average_save]'] = HtmlHelpers::VarField($this->gid,'{%variable_collect_average%}','average_save','varediting3');

$array['config[choice1]'] = array('type'=>'text', 'hint' => '{%choice_shown_to_friend%} 1');
$array['config[choice2]'] = array('type'=>'text', 'hint' => '{%choice_shown_to_friend%} 2');
$array['config[choice3]'] = array('type'=>'text', 'hint' => '{%choice_shown_to_friend%} 3');
$array['config[choice4]'] = array('type'=>'text', 'hint' => '{%choice_shown_to_friend%} 4');
$array['config[choice5]'] = array('type'=>'text', 'hint' => '{%choice_shown_to_friend%} 5');

$array['config[how_many_friends]'] = array('type'=>'text', 'hint' => '{%how_many_friends_needed%}');
$array['config[extrapoints]'] = array('type'=>'text', 'hint' => '{%how_many_extra_points_to_award%}');

return $array;