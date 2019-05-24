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

$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

$args = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => $themes, 'hint' => '{%hint_article_action_theme%}'),
    'config[button_action]' => array('type'=>'dropdownlist','items' => $buttons, 'hint' => '{%hint_article_button_action%}'),
    'config[popup_action_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_popup_action_id%}'),
    'config[show_interstitials]' => array('type'=>'checkbox', 'hint' => '{%hint_show_interstitials%}'),

    'config[deal_placeholder]' => array('type'=>'checkbox', 'hint' => '{%hint_deal_placeholder%}'),
);

// Custom Settings
$custom_settings = array(
    'config[listing_image]' => array('type'=>'text', 'title'=>'%listing_image%'),
    'config[heading_image]' => array('type'=>'text', 'title'=>'%heading_image%'),
    'config[static_image]' => array('type'=>'text', 'title'=>'%static_image%'),

    'config[listing_description]' => array('type'=>'text', 'title'=>'%listing_description%'),
    'config[link]' => array('type'=>'text', 'title'=>'%listing_link%'),
    'config[date]' => array('type'=>'text', 'title'=>'%description%'),
    'config[description]' => array('type'=>'redactor', 'title'=>'%description%'),
);

if ( isset($this->taskmodel->config['deal_placeholder']) AND !empty($this->taskmodel->config['deal_placeholder']) ) {
    $args = array_merge( $args, $custom_settings );
}

return $args;