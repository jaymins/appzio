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

$mode=array(
    'main' => '{%main_view%}',
    'mailbox' =>  '{%mailbox%}',
    'sending' =>  '{%sending%}',
    'custom' =>  '{%custom%}',
    'ask' =>  '{%request_feedback%}',
    'reporting' =>  '{%reporting%}',
);


$modes=array(
    'edit_profile' => '{%edit_profile%}',
    'view_profile' =>  '{%view_profile%}'
);

$current_path = realpath(__DIR__ . '/..');
$themes_dir = $current_path . '/themes';
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
$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

return array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[mode]' => array('type'=>'dropdownlist','items' => $mode, 'hint' => '{%hint_article_action_theme%}'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => $themes, 'hint' => '{%hint_article_action_theme%}'),
    //'config[userdata]' => array('type'=>'textarea', 'title'=>'%subject%'),
    //'config[toplist]' => array('type'=>'dropdownlist','items'  => $actions,'title'=>'%subject%'),
    //'config[userprofile]' => array('type'=>'dropdownlist','items'  => $actions,'title'=>'%subject%'),

    //'config[main_view]' => array('type'=>'dropdownlist','items'  => $actions,'title'=>'%subject%'),
    //'config[mailbox]' => array('type'=>'dropdownlist','items'  => $actions,'title'=>'%subject%'),
    //'config[sender]' => array('type'=>'dropdownlist','items'  => $actions,'title'=>'%subject%'),
    //'config[customcard]' => array('type'=>'dropdownlist','items'  => $actions,'title'=>'%subject%'),

    'config[include_fundamentals]' => array('type'=>'checkbox', 'title'=>'%subject%'),

/*    'config[intro_action]' => array('type'=>'dropdownlist',
        'items' => $actions,
    ),*/

/*    'config[mode]' => array('type'=>'dropdownlist','items' => $modes, 'hint' => '{%mode_hint_profile%}'),
    'config[button_action]' => array('type'=>'dropdownlist','items' => $buttons, 'hint' => '{%hint_article_button_action%}'),
    'config[dynamic]' => array('type'=>'checkbox', 'title'=>'%subject%'),
    'config[chat_content]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[user_images]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[pull_to_refresh]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),
    'config[chat]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_article_button_action%}'),
    'config[matching_action]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_matching_action%}'),
    'config[my_matches_action]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_my_matches_action%}'),
    'config[detail_view]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%detail_view_for_places%}'),
    'config[tips_action]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%tips_action%}'),

    // Configs
    'config[show_email]' => array('type'=>'checkbox'),*/
);