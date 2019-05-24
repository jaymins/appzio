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

$gamevariables = CHtml::listData(Aevariable::model()->findAllByAttributes(array('game_id' => $this->gid)), 'id','name');
$static = array('0' => '{%not_used%}');

  $array = array(
    'config[subject]' => array('type'=>'text'),
    'config[brief]' => array('type'=>'redactor'),

    'config[commentboxtitle]' => array('type'=>'text'),
    'config[points_per_post]' => array('type'=>'text', 'hint' => '{%hint_diary_points_per_post%}'),
    'config[points_per_reply]' => array('type'=>'text', 'hint' => '{%hint_diary_points_per_reply%}'),
    'config[minimum_post_lenght_for_points]' => array('type'=>'text', 'hint' => '{%hint_diary_min_post_length%}'),
    'config[max_points]' => array('type'=>'text', 'hint' => '{%hint_diary_max_points%}'),
    'config[complete_on_points]' => array('type'=>'text', 'hint' => '{%hint_diary_complete_on_points%}'),
   // 'config[complete_on_num_entries]' => array('type'=>'text', 'hint' => '{%hint_diary_complete_on_num_entries%}'),

    'config[allow_delete]' => array('type'=>'checkbox','hint' => '{%hint_diary_allow_delete%}' ),
      'config[simplified_feed]' => array('type'=>'checkbox','hint' => '{%hint_bbs_simplified_feed%}' ),

    /*    'config[show_picture_upload]' => array('type'=>'checkbox','hint' => '{%hint_diary_show_picture_upload%}' ),
        'config[points_per_picture]' => array('type'=>'text', 'hint' => '{%hint_diary_points_per_picture%}'),*/

    
  );

  $array['config[profile_picture]'] = HtmlHelpers::VarField($this->gid,'{%hint_profile_pic%}','profile_picture','profilepic');
  $array['config[name]'] = HtmlHelpers::VarField($this->gid,'{%hint_profile_nickname%}','name','namefld');

/*if(count($gamevariables) > 0){
    $array['config[variable1]'] = array('type'=>'dropdownlist', 'items' => $static+$gamevariables, 'hint' => '{%player_answer%}', 'label' => '{%player_answer%}');
	$array['config[variable2]'] = array('type'=>'dropdownlist', 'items' => $static+$gamevariables, 'hint' => '{%feedback_coach%}');
}*/

return $array;