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

$mode = array(
    'main' => '{%main_view%}',
    'chat' => '{%chat_with_requesters%}',
    'activities' => '{%search_activities%}',
    'locations' => '{%search_locations%}',
    'confirmation' => '{%confirmation_popup%}',
);

$selected_mode = ( isset($model->config['mode']) ? $model->config['mode'] : '' );

$defaults = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mobiledates'), 'hint' => '{%hint_article_action_theme%}'),
    'config[mode]' => array('type' => 'dropdownlist', 'items' => $mode, 'hint' => '{%hint_article_action_theme_mode%}'),
);

switch ( $selected_mode ) {
    case 'main':

        $args = array(
            // 'config[feedback_email]' => array('type'=>'text', 'title'=>'%hint_feedback_email%'),
            // 'config[feedback_subject]' => array('type'=>'text', 'title'=>'%hint_feedback_subject%'),
            // 'config[feedback_body]' => array('type'=>'textarea', 'title'=>'%hint_feedback_body%'),
            'config[chat_action_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_chat_action_id%}'),
            'config[settings_action_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_settings_action_id%}'),
            'config[details_action_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_details_action_id%}'),
            'config[action_activity_popup]' => array('type'=>'dropdownlist','items' => $actions),
            'config[action_location_popup]' => array('type'=>'dropdownlist','items' => $actions),
        );

        break;

    case 'activities':

        $args = array(
            'config[dates_locations_list]' => array('type'=>'textarea', 'hint' => '{%hint_dates_locations_list%}'),
            'config[dates_sponsored_locations_list]' => array('type'=>'textarea', 'hint' => '{%hint_dates_sponsored_locations_list%}'),
        );

        break;

    case 'locations':

        $args = array(
            'config[dates_sponsored_locations]' => array('type'=>'textarea', 'hint' => '{%hint_dates_sponsored_locations%}'),
            'config[update_locations_coords]' => array(
                    'type'  => 'text',
                    'title' => '%geocode%',
                    'class' => 'rss-connect',
                    'hint'  => '<a class="btn btn-mini btn-primary do-user-action" href="'. URLHelpers::add_query_arg( 'update-locations-coods', 1 ) .'">Geocode locations</a><br />Note that pressing the "Update" button would automatically geocode your current Sponsored Locations!'
                ),
        );

        break;
    
    default:
        $args = array();
        break;
}

$args = array_merge( $defaults, $args );

return $args;