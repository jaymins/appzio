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


$choices_correct = array(
    array('id' => 'all', 'name' => '{%all_answers_are_correct%}'),
    array('id' => '1','name' => '{%choice%} 1 ({%bad%}'),
    array('id' => '2','name' => '{%choice%} 2'),
    array('id' => '3','name' => '{%choice%} 3'),
    array('id' => '4','name' => '{%choice%} 4'),
    array('id' => '5','name' => '{%choice%} 5 ({%excellent%}'),
);

$choices = array(
    array('id' => 'all', 'name' => '{%all_answers_are_correct%}'),
    array('id' => '1','name' => '{%choice%} 1'),
    array('id' => '2','name' => '{%choice%} 2'),
    array('id' => '3','name' => '{%choice%} 3'),
    array('id' => '4','name' => '{%choice%} 4'),
    array('id' => '5','name' => '{%choice%} 5'),
);

$modes = array(
    array('id' => 'login', 'name' => 'login'),
    array('id' => 'logout', 'name' => 'logout'),
);

$gamevariables = CHtml::listData($static + Aevariable::model()->findAllByAttributes(array('game_id' => $this->gid)), 'id','name');
$static = array('0' => '{%dont_save_to_variable%}');

$array = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[msg]' => array('type'=>'redactor','class' => 'span2', 'rows'=>10, 'hint' => '{%msg_hint%}'),
    'config[shortmsg]' => array('type'=>'textarea','rows'=>5, 'width' => '100%', 'maxlength' => 200, 'hint' => '{%shortmsg_hint%}'),
    'config[mode]' => array('type'=>'dropdownlist',
        'items' => CHtml::listData($modes,'id','name'),
        'hint' => '{%multiselect_correct_answer%}'),
	'config[update_average]' => array('type'=>'checkbox'),
);

$array['config[variable]'] = HtmlHelpers::VarField($this->gid,'{%collect_to_variable_hint%}','variable','varediting');



return $array;

?>
