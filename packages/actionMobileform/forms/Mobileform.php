<?php

$mode=array(
		'variablelister' => '{%variables%}',
		'points' =>  '{%points%}',
		'points-withoutme' => '{%points_without_users_info%}'
);

$fieldtype=array(
    'checkbox' => '{%checkbox%}',
    'text' =>  '{%text_field%}',
    'textarea' =>  '{%text_area%}'

);


return array(
	'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
	'config[headertext]' => array('type'=>'textarea','rows'=>5, 'width' => '100%', 'maxlength' => 200, 'hint' => '{%shortmsg_hint%}'),

    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mobileform'), 'hint' => '{%hint_article_action_theme%}'),

	'config[field1_title]' => array('type'=>'text', 'title'=>'{%title%}','style' => 'background:#c4e5f3'),
    'config[field1_variable]' => HtmlHelpers::VarField($this->gid,'{%delete_this_variable%}','variable1','varediting','background:#c4e5f3','setvar'),
    'config[field1_type]' => array('type'=>'dropdownlist', 'items' => $fieldtype, 'title'=>'{%field_type%}','style' => 'background:#c4e5f3'),
	'config[field1_required]' => array('type'=>'checkbox', 'title'=>'{%is_field_required%}','style' => 'background:#c4e5f3'),

    'config[field2_title]' => array('type'=>'text', 'title'=>'{%title%}','style' => 'background:#f3d1c4'),
    'config[field2_variable]' => HtmlHelpers::VarField($this->gid,'{%delete_this_variable%}','variable1','varediting','background:#f3d1c4','setvar'),
    'config[field2_type]' => array('type'=>'dropdownlist', 'items' => $fieldtype, 'title'=>'{%field_type%}','style' => 'background:#f3d1c4'),
    'config[field2_required]' => array('type'=>'checkbox', 'title'=>'{%is_field_required%}','style' => 'background:#f3d1c4'),

	'config[field3_title]' => array('type'=>'text', 'title'=>'{%title%}','style' => 'background:#c4e5f3'),
    'config[field3_variable]' => HtmlHelpers::VarField($this->gid,'{%delete_this_variable%}','variable1','varediting','background:#c4e5f3','setvar'),
    'config[field3_type]' => array('type'=>'dropdownlist', 'items' => $fieldtype, 'title'=>'{%field_type%}','style' => 'background:#c4e5f3'),
	'config[field3_required]' => array('type'=>'checkbox', 'title'=>'{%is_field_required%}','style' => 'background:#c4e5f3'),

    'config[field4_title]' => array('type'=>'text', 'title'=>'{%title%}','style' => 'background:#f3d1c4'),
    'config[field4_variable]' => HtmlHelpers::VarField($this->gid,'{%delete_this_variable%}','variable1','varediting','background:#f3d1c4','setvar'),
    'config[field4_type]' => array('type'=>'dropdownlist', 'items' => $fieldtype, 'title'=>'{%field_type%}','style' => 'background:#f3d1c4'),
    'config[field4_required]' => array('type'=>'checkbox', 'title'=>'{%is_field_required%}','style' => 'background:#f3d1c4'),

	'config[field5_title]' => array('type'=>'text', 'title'=>'{%title%}','style' => 'background:#c4e5f3'),
    'config[field5_variable]' => HtmlHelpers::VarField($this->gid,'{%delete_this_variable%}','variable1','varediting','background:#c4e5f3','setvar'),
    'config[field5_type]' => array('type'=>'dropdownlist', 'items' => $fieldtype, 'title'=>'{%field_type%}','style' => 'background:#c4e5f3'),
	'config[field5_required]' => array('type'=>'checkbox', 'title'=>'{%is_field_required%}','style' => 'background:#c4e5f3'),

    'config[field6_title]' => array('type'=>'text', 'title'=>'{%title%}','style' => 'background:#f3d1c4'),
    'config[field6_variable]' => HtmlHelpers::VarField($this->gid,'{%delete_this_variable%}','variable1','varediting','background:#f3d1c4','setvar'),
    'config[field6_type]' => array('type'=>'dropdownlist', 'items' => $fieldtype, 'title'=>'{%field_type%}','style' => 'background:#f3d1c4'),
    'config[field6_required]' => array('type'=>'checkbox', 'title'=>'{%is_field_required%}','style' => 'background:#f3d1c4'),

	'config[field7_title]' => array('type'=>'text', 'title'=>'{%title%}','style' => 'background:#c4e5f3'),
    'config[field7_variable]' => HtmlHelpers::VarField($this->gid,'{%delete_this_variable%}','variable1','varediting','background:#c4e5f3','setvar'),
    'config[field7_type]' => array('type'=>'dropdownlist', 'items' => $fieldtype, 'title'=>'{%field_type%}','style' => 'background:#c4e5f3'),
    'config[field7_required]' => array('type'=>'checkbox', 'title'=>'{%is_field_required%}','style' => 'background:#c4e5f3'),

    'config[field8_title]' => array('type'=>'text', 'title'=>'{%title%}','style' => 'background:#f3d1c4'),
    'config[field8_variable]' => HtmlHelpers::VarField($this->gid,'{%delete_this_variable%}','variable1','varediting','background:#f3d1c4','setvar'),
    'config[field8_type]' => array('type'=>'dropdownlist', 'items' => $fieldtype, 'title'=>'{%field_type%}','style' => 'background:#f3d1c4'),
    'config[field8_required]' => array('type'=>'checkbox', 'title'=>'{%is_field_required%}','style' => 'background:#f3d1c4'),

);