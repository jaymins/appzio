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

$colors = array('black' => 'Black','yellow' => 'Yellow',
    'turquoise' => 'Turquoise', 'red' => 'Red', 'purple' => 'Purple',
    'orange' => 'Orange','lila' => 'Lila','grey' => 'Grey','green' => 'Green');
asort($colors);
 
 $icon='rose.png';
 if (isset($this->taskmodel->config['badge']) && ($this->taskmodel->config['badge']!='')) {
	   $icon=$this->taskmodel->config['badge'];
  }
  
 $color='green';
  if (isset($this->taskmodel->config['badgecolor']) && ($this->taskmodel->config['badgecolor']!='')) {
	   $color=$this->taskmodel->config['badgecolor'];
  }
  
 $widgetparams = array('icon' => $icon, 'rooturl' => 'badges/'.$color, 'width' => '64', 'actiontype' => 'achievement');
 $icons = $this->widget('application.widgets.IconSelectorFormWidget',$widgetparams,true);
 
return array(
    'config[subject]' => array('type'=>'text', 'hint'=>'{%subject%}'),
    'config[title]' => array('type'=>'text', 'hint'=>'{%title%}'),
    'config[text]' => array('type'=>'redactor',
                        'class' => 'span2',
                        'rows'=>10,
                        'hint' => '{%msg_hint%}',
                        'options' => array(
                            'fileUpload' => 'testFileUpload.php',
                            'imageUpload' => 'testImageUpload.php',
                            'width'=>'100%',
                            'height'=>'400px')

    ),
	'config[show_greyout]' => array('type' => 'checkbox', 'hint' =>'{%show_achievement_grey_out%}'),
	'config[skip_diploma]' => array('type' => 'checkbox', 'hint' =>'{%skip_showing_diploma%}','onClick' => 'this.form.submit()'),
	'config[badgecolor]' => array('type' => 'dropdownlist', 'items' => $colors,'hint' => '{%badge_color%}','onChange' => 'this.form.submit()'),
    'config[badge]' => array('type' => 'text', 'hint' => $icons.'{%icon_change_hint%}'),
	


   
);