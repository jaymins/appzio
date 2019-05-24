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
    'create' => array('id' => 'createVar', 'name' => '{%create_variable%}'),
    'dont' => array('id' => '0', 'name' => '{%dont_save_to_variable%}'));

$static2 = array(
    'dont' => array('id' => '0', 'name' => '{%dont_save_to_variable%}'),
    'create' => array('id' => 'createVar', 'name' => '{%create_variable%}'));

$filetype = array('any'=>'{%any%}','picture' => '{%picture%}','sound' => '{%sound%}', 'video' => '{%video%}');
$variables = $static + Aegame::getVariables($this->gid);

$arr =  array(
    'config[subject]' => array('type'=>'text', 'hint' => '{%action_msg_subject%}'),
    'config[msg]' => array('type'=>'redactor', 'hint' => '{%action_msg_body%}'),
    'config[shortmsg]' => array('type'=>'textarea','rows'=>5, 'width' => '100%', 'maxlength' => 200, 'hint' => '{%shortmsg_hint%}'));

$arr['config[variable1]'] = HtmlHelpers::VarField($this->gid,'{%lat_hint%}','variable1','varediting');
$arr['config[variable2]'] = HtmlHelpers::VarField($this->gid,'{%long_hint%}','variable2','varediting2');

return $arr;

?>