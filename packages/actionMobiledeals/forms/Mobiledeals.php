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

$types = array(
    'listing' => '{%mobile_deals_listing%}',
    'placeholder' =>  '{%mobile_deals_placeholder%}',
);

$modes = array(
    'default' => '{%default_mode%}',
    'bookmarks_mode' =>  '{%bookmarks_mode%}',
    'latest_deals_mode' =>  '{%latest_deals_mode%}',
);

$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

$args = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mobiledeals'), 'hint' => '{%hint_article_action_theme%}'),

    // Custom Settings
    'config[deals_action_mode]' => array('type' => 'dropdownlist',
        'items' => $modes,
        'hint' => '{%hint_deals_action_model%}'
    ),

    'config[deals_action_subtype]' => array('type' => 'dropdownlist',
        'items' => $types,
        'hint' => '{%choose_action_subtype%}'
    ),
);

$config = Aeaction::getActionConfig($model->id);

if ( !isset($config->deals_action_subtype) OR $config->deals_action_subtype == 'listing'  )  {
    
    $sub_args = array(
        'config[deals_branch_id]' => array('type' => 'dropdownlist',
            'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
            'hint' => '{%choose_branch%}'
        ),
        'config[deal_item_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_deal_item_id%}'),
    );

    if ( isset($config->deals_action_mode) AND $config->deals_action_mode == 'bookmarks_mode' ) {
        $sub_args['config[listing_action_id]'] = array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_deal_item_id%}');
    }

} else {
    $sub_args = array(
        'config[rss_link]' => array('type'=>'text'),
        'config[rss_connect]' => array(
            'type'  => 'text',
            'title' => '%instagram_connect%',
            'class' => 'rss-connect',
            'hint'  => '<a class="btn btn-mini btn-primary" href="'. URLHelpers::add_query_arg( 'update-rss-news', 1 ) .'">Update RSS Data</a><br />Please note that pressing the "Update" button would automatically store the feed\'s data as <strong>actions</strong> of the current branch!'
        ),
    );
}

$args = array_merge( $args, $sub_args );

return $args;