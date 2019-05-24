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

$current_path = realpath(__DIR__ . '/..');
$themes_dir = $current_path . DIRECTORY_SEPARATOR . 'themes';
$contents = scandir($themes_dir);

$themes = array();

foreach ($contents as $folder) {
    if ( $folder == '.' OR $folder == '..' ) {
        continue;
    }
    $themes[$folder] = ucfirst($folder);
}

$menus = CHtml::listData(Aenavigation::model()->findAllByAttributes(array('app_id' => $this->gid)), 'id', 'name');
$variables = CHtml::listData(Aevariable::model()->findAllByAttributes(array('game_id' => $this->gid)), 'id', 'name');

return array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => $themes, 'hint' => '{%hint_article_action_theme%}'),
    'config[button_action]' => array('type'=>'dropdownlist','items' => $buttons, 'hint' => '{%hint_article_button_action%}'),

    // Custom Settings
    'config[rss_link]' => array('type'=>'text'),
    'config[rss_connect]' => array(
        'type'  => 'text',
        'title' => '%instagram_connect%',
        'class' => 'rss-connect',
        'hint'  => '<a class="btn btn-mini btn-primary" href="'. URLHelpers::add_query_arg( 'update-rss-news', 1 ) .'">Update RSS Data</a>'
    ),

    'config[api_link]' => array('type'=>'text'),
    'config[api_connect]' => array(
        'type'  => 'text',
        'title' => '%api_connect%',
        'class' => 'rss-connect',
        'hint'  => '<a class="btn btn-mini btn-primary" href="'. URLHelpers::add_query_arg( 'update-api-news', 1 ) .'">Update API Data</a>'
    ),
);