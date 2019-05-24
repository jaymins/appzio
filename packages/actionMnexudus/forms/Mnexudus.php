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
    '' => '',
    'login' => '{%login%}',
    'register' => '{%registration%}',
    'home' => '{%home%}',
    'pricing' => '{%pricing%}',
    'makebooking' => '{%make_booking%}',
    'listbookings' => '{%list_bookings%}',
    'viewbooking' => '{%view_booking%}',
    'booking' => '{%invididual_booking%}',
    'faq' => '{%faq%}',
    'rules' => '{%rule_book%}',
    'help' => '{%help%}',
    'unlock' => '{%unlock%}',
    'preferences' => '{%preferences%}',
);

$config = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing($shortname), 'hint' => '{%hint_article_action_theme%}'),
    'config[button_action]' => array('type'=>'dropdownlist','items' => $buttons, 'hint' => '{%hint_article_button_action%}'),
    'config[mode]' => array('type' => 'dropdownlist', 'items' => $mode, 'hint' => '{%hint_article_action_mode%}'),
    'config[debug]' => array('type' => 'checkbox'),
);

if ( isset($model->config['mode']) AND $model->config['mode'] == 'faq' ) {
    $config['config[description]'] = array('type' => 'textarea', 'style' => 'width:97%;height:450px;', 'hint' => '{%hint_article_action_mode%}');
    $config['config[faq_items]'] = array('type' => 'textarea', 'style' => 'width:97%;height:450px;','items' => $mode, 'hint' => 'Defined business characters using the following format:
    faq title;description;faq title;description
    ');
}

if ( isset($model->config['mode']) AND $model->config['mode'] == 'pricing' ) {
    $config['config[price_15]'] = array('type' => 'text', 'hint' => '{%price_for_15_min%}');
    $config['config[price_30]'] = array('type' => 'text', 'hint' => '{%price_for_30_min%}');
    $config['config[price_45]'] = array('type' => 'text', 'hint' => '{%price_for_45_min%}');
    $config['config[price_60]'] = array('type' => 'text', 'hint' => '{%price_for_60_min%}');
}

if ( isset($model->config['mode']) AND $model->config['mode'] == 'rules' ) {
    $config['config[description]'] = array('type' => 'textarea', 'style' => 'width:97%;height:450px;', 'hint' => '{%hint_article_action_mode%}');
    $config['config[rule_items]'] = array('type' => 'textarea', 'style' => 'width:97%;height:450px;','items' => $mode, 'hint' => 'Defined business characters using the following format:
    rule title;description;rule title;description
    ');
}


return $config;