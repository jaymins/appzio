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

return array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[msg]' => array('type'=>'redactor',
                        'class' => 'span2',
                        'rows'=>10,
                        'hint' => '{%msg_hint%}',
                        'options' => array(
                            'fileUpload' => 'testFileUpload.php',
                            'imageUpload' => 'testImageUpload.php',
                            'width'=>'100%',
                            'height'=>'400px'),

    ),

    'config[feedurl]' => array('type'=>'text', 'hint'=>'{%rss_feedurl_hint%}'),
    'config[feedconfig]' => array('type'=>'textarea', 'hint'=>'{%json_feedconfig%}'),
    'config[itemconfig]' => array('type'=>'textarea', 'hint'=>'{%json_itemconfig%}'),

    'config[item_page_subject]' => array('type'=>'textarea', 'hint'=>'{%json_feedconfig%}'),
    'config[item_page_text]' => array('type'=>'textarea', 'hint'=>'{%json_feedconfig%}'),

    'config[shortmsg]' => array('type'=>'textarea','rows'=>5, 'width' => '100%', 'maxlength' => 200, 'hint' => '{%shortmsg_hint%}'),
);