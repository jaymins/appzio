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

$path= Yii::app()->Controller->getDomain() .'/documents/games/'.$this->gid.'/original_images/';

/* for better backwards compatibility */
if(isset($this->taskmodel->config['subject']) AND !isset($this->taskmodel->config['mode'])){
    $aeac = Aeaction::model()->findByPk($this->taskmodel->id);
    $conf = json_decode($aeac->config);
    $conf->mode = 'text-advanced';
    $aeac->config = json_encode($conf);
    $aeac->update();
    if(isset($_REQUEST['tab'])){ $tab = $_REQUEST['tab']; } else { $tab = false; }
    if(isset($_REQUEST['tt'])){ $tt = $_REQUEST['tt']; } else { $tt = false; }
    Yii::app()->request->redirect($this->mainurl .'&tid=' .$this->tid .'&tab=' .$tab);
}

$config = $model->config;

/*$field1 =  HtmlHelpers::actionMediaField('choice1_img',$config,$this->gid,'images','app');
$field2 =  HtmlHelpers::actionMediaField('choice2_img',$config,$this->gid,'images','app');
$field3  =  HtmlHelpers::actionMediaField('choice3_img',$config,$this->gid,'images','app');
$field4  =  HtmlHelpers::actionMediaField('choice4_img',$config,$this->gid,'images','app');
$field5  =  HtmlHelpers::actionMediaField('choice5_img',$config,$this->gid,'images','app');*/

if (isset($this->taskmodel->config['choice1_img']) && ($this->taskmodel->config['choice1_img']!='')) {

    $img='&nbsp;<img src="'.$path.$this->taskmodel->config['choice1_img'].'" width="50" />';
    $field1=array('type'=>'checkbox', 'value'=>'1','hint' => '{%check_to_delete_image%}'.$img);
} else {
    $field1 = array('type'=>'file', 'hint' => '{%choice%} 1 {%image%}');
}


if (isset($this->taskmodel->config['choice2_img']) && ($this->taskmodel->config['choice2_img']!='')) {
    $img='&nbsp;<img src="'.$path.$this->taskmodel->config['choice2_img'].'" width="50" />';
    $field2=array('type'=>'checkbox', 'value'=>'1','hint' => '{%check_to_delete_image%}'.$img);
} else {
    $field2 = array('type'=>'file', 'hint' => '{%choice%} 2 {%image%}');
}


if (isset($this->taskmodel->config['choice3_img']) && ($this->taskmodel->config['choice3_img']!='')) {
    $img='&nbsp;<img src="'.$path.$this->taskmodel->config['choice3_img'].'" width="50" />';
    $field3=array('type'=>'checkbox', 'value'=>'1','hint' => '{%check_to_delete_image%}'.$img);
} else {
    $field3 = array('type'=>'file', 'hint' => '{%choice%} 3 {%image%}');
}


if (isset($this->taskmodel->config['choice4_img']) && ($this->taskmodel->config['choice4_img']!='')) {
    $img='&nbsp;<img src="'.$path.$this->taskmodel->config['choice4_img'].'" width="50" />';
    $field4=array('type'=>'checkbox', 'value'=>'1','hint' => '{%check_to_delete_image%}'.$img);
} else {
    $field4 = array('type'=>'file', 'hint' => '{%choice%} 4 {%image%}');
}


if (isset($this->taskmodel->config['choice5_img']) && ($this->taskmodel->config['choice5_img']!='')) {
    $img='&nbsp;<img src="'.$path.$this->taskmodel->config['choice5_img'].'" width="50" />';
    $field5=array('type'=>'checkbox', 'value'=>'1','hint' => '{%check_to_delete_image%}'.$img);
} else {
    $field5 = array('type'=>'file', 'hint' => '{%choice%} 5 {%image%}');
}


$static = array(
    'create' => array('id' => 'createVar', 'name' => '{%create_variable%}'),
    'dont' => array('id' => '0', 'name' => '{%dont_save_to_variable%}'));

$choices = array(
    array('id' => '-1', 'name' => '{%all_answers_are_correct%}'),
    array('id' => '1','name' => '{%choice%} 1'),
    array('id' => '2','name' => '{%choice%} 2'),
    array('id' => '3','name' => '{%choice%} 3'),
    array('id' => '4','name' => '{%choice%} 4'),
    array('id' => '5','name' => '{%choice%} 5'),
);

$answersave = array(
    array('id' => '', 'name' => ''),
    array('id' => 'collect_id', 'name' => '{%collect_to_variable_id%}'),
    array('id' => 'collect_content', 'name' => '{%collect_to_variable_content%}')
);

$points= array (
 array('id' => '', 'name' => ''),
 array('id' => 'primary', 'name' => '{%primary_points%}'),
 array('id' => 'secondary', 'name' => '{%secondary_points%}'),
 array('id' => 'tertiary', 'name' => '{%tertiary_points%}'));


$modes = array(
    array('id' => '', 'name' => '{%select_mode%}'),
    array('id' => 'text-simple','name' => '{%mode_text_simple%}'),
    array('id' => 'image-simple','name' => '{%mode_image_simple%}'),
    array('id' => 'text-advanced','name' => '{%mode_text_advanced%}'),
    array('id' => 'image-advanced','name' => '{%mode_image_advanced%}'),
);


if(isset($this->taskmodel->config['mode'])){
    $mode = $this->taskmodel->config['mode'];
} else {
    $mode = false;
}


if(!isset($this->taskmodel->config['mode']) OR !$this->taskmodel->config['mode']){

    $array = array(

        'config[mode]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($modes,'id','name'), 'hint' => '{%multiselect_select_mode%}', 'maxlength' => 222, 'controlGroupHtml' => 'style="border: 2px solid #c8c8c8;"', 'onChange'=>'this.form.submit()'),
        'config[subject]' => array('type'=>'hidden'),
        'config[msg]' => array('type'=>'hidden'),
        'config[shortmsg]' => array('type'=>'hidden'),
        'config[choice1]' => array('type'=>'hidden'),
        'config[points1]' => array('type'=>'hidden'),
        'config[choice2]' => array('type'=>'hidden'),
        'config[points2]' => array('type'=>'hidden'),
        'config[choice3]' => array('type'=>'hidden'),
        'config[points3]' => array('type'=>'hidden'),
        'config[choice4]' => array('type'=>'hidden'),
        'config[points4]' => array('type'=>'hidden'),
        'config[choice5]' => array('type'=>'hidden'),
        'config[points5]' => array('type'=>'hidden'),

        'config[color1]' => array('type'=>'hidden'),
        'config[color2]' => array('type'=>'hidden'),
        'config[color3]' => array('type'=>'hidden'),
        'config[color4]' => array('type'=>'hidden'),
        'config[color5]' => array('type'=>'hidden'),

        'config[show_2_buttons_on_row]' => array('type'=>'hidden'),
        'config[correct_answer]' => array('type'=>'hidden'),
        'config[update_average]' => array('type'=>'hidden'),
        'config[show_options_randomly]' => array('type'=>'hidden'),
        'config[show_the_correct_answer]' => array('type'=>'hidden'),
        'config[answers_to_hide]' => array('type'=>'hidden'),
        'config[points_to_subtract]' => array('type'=>'hidden'),
        'config[which_points_to_subtract]' => array('type'=>'hidden'),
		'config[show_graph]' => array('type'=>'checkbox', 'hint'=> '{%show_graph_after_answering%}'),
        'config[variable]' => array('type'=>'hidden')
    );


} elseif(isset($this->taskmodel->config['mode']) AND $this->taskmodel->config['mode'] == 'image-advanced') {
    $array = array(
    'config[mode]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($modes,'id','name'), 'value' => $mode, 'hint' => '{%multiselect_select_mode%}', 'onChange'=>'this.form.submit()', 'controlClass' => 'controlexpandable'),
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[msg]' => array('type'=>'redactor','class' => 'span2', 'rows'=>10, 'hint' => '{%msg_hint%}'),
    'config[shortmsg]' => array('type'=>'textarea','rows'=>5, 'width' => '100%', 'maxlength' => 200, 'hint' => '{%shortmsg_hint%}'),

    'config[image_width]' => array('type'=>'text', 'hint' => '{%multiselect_image_width%}'),
    'config[image_height]' => array('type'=>'text', 'hint' => '{%multiselect_image_height%}'),
    'config[image_round]' => array('type'=>'checkbox', 'hint' => '{%multiselect_image_round%}'),
    'config[choice1_img]' => $field1,
	'config[points1]' => array('type'=>'text', 'hint' => '{%multiselect_points_hint%} 1'),
	'config[choice2_img]' => $field2,
	'config[points2]' => array('type'=>'text', 'hint' => '{%multiselect_points_hint%} 2'),
    'config[choice3_img]' => $field3,
	'config[points3]' => array('type'=>'text', 'hint' => '{%multiselect_points_hint%} 3'),
	'config[choice4_img]' => $field4,
	'config[points4]' => array('type'=>'text', 'hint' => '{%multiselect_points_hint%} 4'),
	'config[choice5_img]' => $field5,
    'config[choice1]' => array('type'=>'hidden','value'=>''),
    'config[choice2]' => array('type'=>'hidden','value'=>''),
    'config[choice3]' => array('type'=>'hidden','value'=>''),
    'config[choice4]' => array('type'=>'hidden','value'=>''),
    'config[choice5]' => array('type'=>'hidden','value'=>''),
    'config[points5]' => array('type'=>'text', 'hint' => '{%multiselect_points_hint%} 5'),
	'config[show_2_buttons_on_row]' => array('type'=>'checkbox', 'hint'=> '{%show_two_button_on_row%}'),
    'config[correct_answer]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($choices,'id','name'), 'hint' => '{%multiselect_correct_answer%}'),
	'config[update_average]' => array('type'=>'checkbox'),
	'config[show_options_randomly]' => array('type'=>'checkbox', 'hint'=> '{%show_options_randomly%}'),
	'config[show_the_correct_answer]' => array('type'=>'checkbox', 'hint'=> '{%show_correct_answer_after_answering%}'),
	'config[answers_to_hide]' => array('type'=>'text', 'hint'=> '{%how_many_possible_answers_to_hide%}'),
	'config[points_to_subtract]' => array('type'=>'text', 'hint'=> '{%how_many_points_to_subtract%}'),
	'config[which_points_to_subtract]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($points,'id','name'), 'hint'=> '{%which_points_to_subtract%}'),
	'config[show_graph]' => array('type'=>'checkbox', 'hint'=> '{%show_graph_after_answering%}'),
	'config[variable]' => HtmlHelpers::VarField($this->gid,'{%collect_to_variable_hint%}','variable','varediting')
    );


} elseif(isset($this->taskmodel->config['mode']) AND $this->taskmodel->config['mode'] == 'text-advanced') {
    $array = array(
        'config[mode]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($modes,'id','name'), 'value' => $this->taskmodel->config['mode'], 'hint' => '{%multiselect_select_mode%}', 'onChange'=>'this.form.submit()', 'controlClass' => 'controlexpandable'),
        'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
        'config[msg]' => array('type'=>'redactor','class' => 'span2', 'rows'=>10, 'hint' => '{%msg_hint%}'),
        'config[shortmsg]' => array('type'=>'textarea','rows'=>5, 'width' => '100%', 'maxlength' => 200, 'hint' => '{%shortmsg_hint%}'),
        'config[choice1]' => array('type'=>'text', 'hint' => '{%choice%} 1'),
        'config[points1]' => array('type'=>'text', 'hint' => '{%multiselect_points_hint%} 1'),
        'config[color1]' => array('type'=>'text', 'hint' => '{%multiselect_color1_hint%} 1', 'class' => 'jscolor'),
        'config[choice2]' => array('type'=>'text', 'hint' => '{%choice%} 2'),
        'config[points2]' => array('type'=>'text', 'hint' => '{%multiselect_points_hint%} 2'),
        'config[color2]' => array('type'=>'text', 'hint' => '{%multiselect_color1_hint%} 2', 'class' => 'jscolor'),

        'config[choice3]' => array('type'=>'text', 'hint' => '{%choice%} 3'),
        'config[points3]' => array('type'=>'text', 'hint' => '{%multiselect_points_hint%} 3'),
        'config[color3]' => array('type'=>'text', 'hint' => '{%multiselect_color1_hint%} 3', 'class' => 'jscolor'),

        'config[choice4]' => array('type'=>'text', 'hint' => '{%choice%} 4'),
        'config[points4]' => array('type'=>'text', 'hint' => '{%multiselect_points_hint%} 4'),
        'config[color4]' => array('type'=>'text', 'hint' => '{%multiselect_color1_hint%} 4', 'class' => 'jscolor'),

        'config[choice5]' => array('type'=>'text', 'hint' => '{%choice%} 5'),
        'config[points5]' => array('type'=>'text', 'hint' => '{%multiselect_points_hint%} 5'),
        'config[color5]' => array('type'=>'text', 'hint' => '{%multiselect_color1_hint%} 5', 'class' => 'jscolor'),

        'config[show_2_buttons_on_row]' => array('type'=>'checkbox', 'hint'=> '{%show_two_button_on_row%}'),
        'config[correct_answer]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($choices,'id','name'), 'hint' => '{%multiselect_correct_answer%}'),
        'config[update_average]' => array('type'=>'checkbox'),
        'config[show_options_randomly]' => array('type'=>'checkbox', 'hint'=> '{%show_options_randomly%}'),
        'config[show_the_correct_answer]' => array('type'=>'checkbox', 'hint'=> '{%show_correct_answer_after_answering%}'),
        'config[answers_to_hide]' => array('type'=>'text', 'hint'=> '{%how_many_possible_answers_to_hide%}'),
        'config[points_to_subtract]' => array('type'=>'text', 'hint'=> '{%how_many_points_to_subtract%}'),
        'config[which_points_to_subtract]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($points,'id','name'), 'hint'=> '{%which_points_to_subtract%}'),
		// 'config[show_graph]' => array('type'=>'checkbox', 'hint'=> '{%show_graph_after_answering%}'),
        'config[variable]' => HtmlHelpers::VarField($this->gid,'{%collect_to_variable_hint%}','variable','varediting')
    );
} elseif(isset($this->taskmodel->config['mode']) AND $this->taskmodel->config['mode'] == 'text-simple') {
    $array = array(
        'config[mode]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($modes,'id','name'), 'value' => $this->taskmodel->config['mode'], 'hint' => '{%multiselect_select_mode%}', 'onChange'=>'this.form.submit()', 'controlClass' => 'controlexpandable'),
        'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
        'config[msg]' => array('type'=>'redactor','class' => 'span2', 'rows'=>10, 'hint' => '{%msg_hint%}'),
        'config[choice1]' => array('type'=>'text', 'hint' => '{%choice%} 1'),
        'config[choice2]' => array('type'=>'text', 'hint' => '{%choice%} 2'),
        'config[choice3]' => array('type'=>'text', 'hint' => '{%choice%} 3'),
        'config[choice4]' => array('type'=>'text', 'hint' => '{%choice%} 4'),
        'config[choice5]' => array('type'=>'text', 'hint' => '{%choice%} 5'),
        'config[correct_answer]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($choices,'id','name'), 'hint' => '{%multiselect_correct_answer%}'),
        'config[show_options_randomly]' => array('type'=>'checkbox', 'hint'=> '{%show_options_randomly%}'),
        'config[show_the_correct_answer]' => array('type'=>'checkbox', 'hint'=> '{%show_correct_answer_after_answering%}'),
		'config[show_graph]' => array('type'=>'checkbox', 'hint'=> '{%show_graph_after_answering%}'),
        'config[variable]' => HtmlHelpers::VarField($this->gid,'{%collect_to_variable_hint%}','variable','varediting')
    );

} elseif(isset($this->taskmodel->config['mode']) AND $this->taskmodel->config['mode'] == 'image-simple') {
    $array = array(
        'config[mode]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($modes,'id','name'), 'value' => $this->taskmodel->config['mode'], 'hint' => '{%multiselect_select_mode%}', 'onChange'=>'this.form.submit()', 'controlClass' => 'controlexpandable'),
        'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
        'config[msg]' => array('type'=>'redactor','class' => 'span2', 'rows'=>10, 'hint' => '{%msg_hint%}'),
        'config[choice1_img]' => $field1,
        'config[choice2_img]' => $field2,
        'config[choice3_img]' => $field3,
        'config[choice4_img]' => $field4,
        'config[choice5_img]' => $field5,
        'config[choice1]' => array('type'=>'hidden', 'value'=>''),
        'config[choice2]' => array('type'=>'hidden', 'value'=>''),
        'config[choice3]' => array('type'=>'hidden', 'value'=>''),
        'config[choice4]' => array('type'=>'hidden', 'value'=>''),
        'config[choice5]' => array('type'=>'hidden', 'value'=>''),

        'config[correct_answer]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($choices,'id','name'), 'hint' => '{%multiselect_correct_answer%}'),
        'config[show_options_randomly]' => array('type'=>'checkbox', 'hint'=> '{%show_options_randomly%}'),
        'config[show_the_correct_answer]' => array('type'=>'checkbox', 'hint'=> '{%show_correct_answer_after_answering%}'),
		'config[show_graph]' => array('type'=>'checkbox', 'hint'=> '{%show_graph_after_answering%}'),
        'config[variable]' => HtmlHelpers::VarField($this->gid,'{%collect_to_variable_hint%}','variable','varediting')
    );
}

return $array;