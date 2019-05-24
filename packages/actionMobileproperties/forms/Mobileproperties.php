<?php

/*

This is the admin form configuration which you can find under admin interface .

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

$actions = CHtml::listData(Aeaction::getAllActions($this->gid), 'id', 'name');
$config = Aeaction::getActionConfig($model->id);

$buttons = array(
    'no_button' => '{%article_no_button%}',
    'submit' => '{%article_standard_complete_button%}',
    'save' => '{%article_save_button%}',
);

$mode = array(
    'main' => '{%main_view%}',
    'add' => '{%add_new_property%}',
    'upsell' => '{%add_new_property_upsell%}',
    'matching' => '{%tenant_home%}',
    'settings' => '{%settings%}',
    'active' => '{%available%}',
    'inactive' => '{%unavailable%}',
    'search' => '{%search%}',
    'favourites' => '{%favourites%}',
    'single' => '{%single_property%}',
    'location' => '{%zip_code_search%}',
    'tenants' => '{%tenants%}',
    'xml' => '{%xml_parser%}',
);

$default_args = array(
    'config[mode]' => array('type' => 'dropdownlist', 'items' => $mode, 'hint' => '{%hint_article_action_theme%}', 'onChange'=>'this.form.submit()'),
    'config[article_action_theme]' => array('type' => 'dropdownlist', 'items' => Controller::getActionThemeListing('mobileproperties'), 'hint' => '{%hint_article_action_theme%}'),
);

$args = array(
    'config[subject]' => array('type' => 'text', 'title' => '%subject%'),
    'config[alertbox]' => array('type' => 'textarea', 'title' => '%alertbox%'),
    'config[show_email]' => array('type' => 'checkbox'),
    'config[data_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid), 'id', 'title'),
        'hint' => '{%choose_branch%}'
    ),
    'config[complete_action]' => array('type' => 'checkbox'),
    'config[chat]' => array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%detail_view_for_places%}'),
    'config[add_new]' => array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%detail_view_for_places%}'),
    'config[detail_view]' => array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%detail_view_for_places%}'),
    'config[suggest_location_action]' => array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%hint_suggest_location_action%}'),
    'config[debug_mode]' => array('type' => 'checkbox', 'hint' => '{%menu_for_submit_button%}'),
);

if ( isset($config->mode) AND $config->mode == 'xml' ) {
    $args = array(
        'config[user_play_id]' => array('type'=>'text', 'hint' => '{%hint_user_play_id%}'),
        'config[xml_import]' => array(
            'type'  => 'text',
            'title' => '%xml-import%',
            'class' => 'rss-connect',
            'hint'  => '<a class="btn btn-mini btn-primary" href="'. URLHelpers::add_query_arg( 'control-importer', 'start' ) .'">Start XML importer</a><span style="padding: 0 3px;">&nbsp;</span><a class="btn btn-mini btn-danger" href="'. URLHelpers::add_query_arg( 'control-importer', 'stop' ) .'">STOP XML importer</a>'
        ),
    );
}

return array_merge( $default_args, $args );