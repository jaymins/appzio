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

$arr = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mobilelogin', true), 'hint' => '{%hint_article_action_theme%}'),
    'config[button_action]' => array('type'=>'dropdownlist','items' => $buttons, 'hint' => '{%hint_article_button_action%}'),

    // Configs
    'config[show_email]' => array('type'=>'checkbox'),

    'config[only_logout]' => array('type'=>'checkbox'),

    'config[register_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        'hint' => '{%hint_register_branch%}'
    ),

    'config[login_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        'hint' => '{%hint_login_branch%}'
    ),

    'config[logout_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        'hint' => '{%hint_logout_branch%}'
    ),

    'config[tenant_home_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        'hint' => '{%hint_tenant_home_branch%}'
    ),

    'config[agent_home_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        'hint' => '{%hint_agent_home_branch%}'
    ),

    'config[terms_popup]' => array('type'=>'dropdownlist','items' => CHtml::listData(Aeaction::getAllActions($this->gid),'id','name'), 'hint' => '{%hint_terms_popup%}'),
    'config[corporate_login]' => array('type'=>'dropdownlist','items' => CHtml::listData(Aeaction::getAllActions($this->gid),'id','name'), 'hint' => '{%hint_corporate_login%}'),

    'config[collect_push_permission]' => array('type'=>'checkbox'),
    'config[touch_id]' => array('type'=>'checkbox'),

    // alert
    'config[alertbox]' => array('type'=>'textarea', 'title'=>'%alertbox%'),
    'config[facebook_enabled]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),
    'config[google_enabled]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),
    'config[twitter_enabled]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),
    'config[instagram_enabled]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),
    'config[instagram_login_provider]' => array('type'=>'dropdownlist','items' => CHtml::listData(Aeaction::getAllActions($this->gid),'id','name'), 'hint' => '{%terms_popup%}'),
    'config[ask_for_role]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),

    // Emails
    'config[email_subject]' => array('type'=>'text', 'title'=>'%hint_email_subject%'),
    'config[email_footer_text]' => array('type'=>'text', 'title'=>'%hint_email_footer_text%'),
);

return $arr;