<?php

/**
 * This is a theme specific form control file, which works little differently from
 * the action's main config. Action's main config will always be included, but you
 * can override fields and add theme specific fields. They are shown only when the
 * correct theme is selected from the admin.
 *
 * NOTE: all fields are named config[yourfieldname], nothing else will work. You can input any
 * field names, and these are available inside the model using $this->getConfigField('fieldname')
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


$mode = array(
    '' => 'Home',
    'dashboard' => '{%dashboard%}',
    'downloads' => '{%downloadable_items%}',
    'characters' => '{%business_characters%}',
    'about' => '{%about%}',
    'create' => '{%create%}',
);

$config = array(
    'config[mode]' => array('type' => 'dropdownlist', 'items' => $mode, 'hint' => '{%hint_article_action_mode%}'),
);

if ( isset($model->config['mode']) AND $model->config['mode'] == '' ) {
    $config['config[stream_link]'] = array('type' => 'text', 'hint' => '{%video_stream_link%}');
}

if ( isset($model->config['mode']) AND $model->config['mode'] == 'dashboard' ) {
    $config['config[description]'] = array('type' => 'textarea', 'style' => 'width:97%;height:300px;', 'hint' => '{%hint_article_action_mode%}');
    $config['config[table_header]'] = array('type' => 'text', 'hint' => '{%hint_article_action_mode%}');
    $config['config[columns]'] = array('type' => 'text', 'hint' => '{%hint_article_action_mode%}');
    $config['config[values]'] = array('type' => 'textarea', 'style' => 'width:97%;height:250px;','items' => $mode, 'hint' => 'Defined business characters using the following format:
    title;value1;value2
    title;value1;value2
    ');

}

if ( isset($model->config['mode']) AND $model->config['mode'] == 'downloads' ) {
    $config['config[header]'] = array('type' => 'text', 'hint' => '{%hint_article_action_mode%}');
    $config['config[downloads]'] = array('type' => 'textarea', 'style' => 'width:97%;height:450px;','items' => $mode, 'hint' => 'Use the following format:
    File name;Link for downloading;File name;Link for downloading');

}

if ( isset($model->config['mode']) AND $model->config['mode'] == 'characters' ) {
    $config['config[description]'] = array('type' => 'textarea', 'style' => 'width:97%;height:450px;', 'hint' => '{%hint_article_action_mode%}');
    $config['config[characters]'] = array('type' => 'textarea', 'style' => 'width:97%;height:450px;','items' => $mode, 'hint' => 'Defined business characters using the following format:
    character name;description;character name;description
    ');
}

if ( isset($model->config['mode']) AND $model->config['mode'] == 'about' ) {
    $config['config[about_field]'] = array('type' => 'textarea','style' => 'width:97%;height:450px;', 'items' => $mode, 'hint' => '{%hint_article_action_mode%}');
}


return $config;