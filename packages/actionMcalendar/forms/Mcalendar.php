<?php

/**
 * This is the admin interface configuration for the action. All configuration options
 * are easily available under model as $this->getConfigParam('param_name');
 *
 * NOTE: all fields are named config[yourfieldname], nothing else will work. You can invent any
 * field names, these are available when rendering the view (under views/)
 *
 * IMPORTANT: make sure to define your action's shortname
 *
 *
 * supported field types, uses Yii Booster, this would be your best source of information:
 * http://www.yiiframework.com/forum/index.php/topic/36258-yiibooster/
 *
 * 'text' => 'textFieldRow',
 * 'password' => 'passwordFieldRow',
 * 'textarea' => 'textAreaRow',
 * 'file' => 'fileFieldRow',
 * 'radio' => 'radioButtonRow',
 * 'checkbox' => 'checkBoxRow',
 * 'listbox' => 'dropDownListRow',
 * 'dropdownlist' => 'dropDownListRow',
 * 'checkboxlist' => 'checkBoxListRow',
 * 'radiolist' => 'radioButtonListRow',
 *
 * //HTML5 types not supported in YiiBooster yet: render as textField
 * 'url' => 'textFieldRow',
 * 'email' => 'textFieldRow',
 * 'number' => 'textFieldRow',
 *
 * //'range'=>'activeRangeField', not supported yet
 * 'date' => 'datepickerRow',
 *
 * //new YiiBooster types
 * 'captcha' => 'captchaRow',
 * 'daterange' => 'dateRangeRow',
 * 'redactor' => 'redactorRow',
 * 'markdowneditor' => 'markdownEditorRow',
 * 'uneditable' => 'uneditableRow',
 * 'radiolistinline' => 'radioButtonListInlineRow',
 * 'checkboxlistinline' => 'checkBoxListInlineRow',
 * 'select2' => 'select2Row'
 *
 */

$buttons=array(
    'no_button' => '{%article_no_button%}',
    'submit' =>  '{%article_standard_complete_button%}',
    'save' =>  '{%article_save_button%}',
);

$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

$shortname = basename(__FILE__);
$shortname = str_replace('.php', '', $shortname);
$shortname = strtolower($shortname);

if(isset($model->type_id) AND $model->type_id > 0){
    $type = Aeactiontypes::model()->findByPk($model->type_id);
    if(isset($type->shortname)){
        $shortname = $type->shortname;
    }
}

$mode = array(
    'daily' => '{%daily%}',
    'weekly' => '{%weekly%}',
    'schedule' => '{%schedule%}'
);


return array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing($shortname), 'hint' => '{%hint_article_action_theme%}'),
    'config[button_action]' => array('type'=>'dropdownlist','items' => $buttons, 'hint' => '{%hint_article_button_action%}'),
    'config[mode]' => array('type' => 'dropdownlist', 'items' => $mode, 'hint' => '{%hint_article_action_mode%}'),
);