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

$buttons=array(
    'no_button' => '{%article_no_button%}',
    'submit' =>  '{%article_standard_complete_button%}',
    'save' =>  '{%article_save_button%}',
);


$granularity=array(
    'by_morning_afternoon' => '{%morning_and_afternoon%}',
);


return array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mobileavailability'), 'hint' => '{%hint_article_action_theme%}'),
    'config[button_action]' => array('type'=>'dropdownlist','items' => $buttons, 'hint' => '{%hint_article_button_action%}'),

    'config[msg]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[granularity]' => array('type'=>'dropdownlist','items' => $granularity, 'hint' => '{%hint_article_button_action%}'),
    'config[full_week]' => array('type'=>'checkbox'),
    'config[complete_action]' => array('type'=>'checkbox'),

/*    // alert
    'config[alertbox]' => array('type'=>'textarea', 'title'=>'%alertbox%'),

    // Configs

    'config[data_branch]' => array('type' => 'dropdownlist',
        'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
        'hint' => '{%choose_branch%}'
    ),

    'config[debug_mode]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),*/
);