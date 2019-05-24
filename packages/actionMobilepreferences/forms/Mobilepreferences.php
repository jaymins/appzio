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

$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

$configs = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[allow_reset]' => array('type'=>'checkbox', 'title'=>'%allow_resetting%'),
    'config[headertext]' => array('type'=>'textarea','rows'=>5, 'width' => '100%', 'maxlength' => 200, 'hint' => '{%shortmsg_hint%}'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mobilepreferences'), 'hint' => '{%hint_article_action_theme%}'),
);

if ( isset($model->config['article_action_theme']) AND $model->config['article_action_theme'] == 'gaybff' ) {
    $configs['config[gaybff_edit_preferences]'] = array('type'=>'checkbox');
    $configs['config[return_action_id]'] = array('type'=>'dropdownlist', 'items' => $actions);
    $configs['config[detail_view]'] = array('type'=>'dropdownlist', 'items' => $actions);
}

$configs['config[min_distance]'] = array('type'=>'text');
$configs['config[max_distance]'] = array('type'=>'text');

return $configs;