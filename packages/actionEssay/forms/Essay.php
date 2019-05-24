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



  $array = array(
    'config[subject]' => array('type'=>'text', 'hint'=>'{%subject%}'),
    'config[email_coach]' => array('type'=>'textarea', 'hint' => '{%email_coach%}'),
    'config[essay]' => array('type'=>'redactor',  'hint' => '{%essay_admin_hint%}'),
    'config[coach_feedback]' => array('type'=>'checkbox','hint' => '{%coach_feedback%}' ),
	'config[fieldtype1]' => array('type'=>'hidden','value' => 'redactor'),
	'config[fieldtype2]' => array('type'=>'hidden','value' => 'redactor'),
	'config[visible1]' => array('type'=>'hidden','value' => '1'),
	'config[visible2]' => array('type'=>'hidden','value' => '0'),
	'config[type_points]' => array('type'=>'dropdownlist', 'items' => array('0'=>'{%fixed_points%}', '1'=>'{%flexible_points%}'),  'hint' => '{%type_of_points_after_coach_review%}'),
	
);

if(isset($model->config['type_points']) AND $model->config['type_points'] == 0){
    $array['config[fixed_points]'] = array('type'=>'text', 'hint' => '{%fixed_points_after_coach_review%}');
} else {
    $array['config[min_flexible_points]'] = array('type'=>'text', 'hint' => '{%min_flexible_points%}');
    $array['config[max_flexible_points]'] = array('type'=>'text', 'hint' => '{%max_flexible_points%}');
}

$array['config[variable1]'] = HtmlHelpers::VarField($this->gid,'{%player_answer%}','variable1','varediting1');
$array['config[variable2]'] = HtmlHelpers::VarField($this->gid,'{%feedback_coach%}','variable2','varediting2');


return $array;
?>