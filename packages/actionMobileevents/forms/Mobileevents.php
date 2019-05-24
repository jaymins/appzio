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

$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

$theme = '';

if ( isset($this->taskmodel->config['article_action_theme']) AND $this->taskmodel->config['article_action_theme'] ) {
    $theme = $this->taskmodel->config['article_action_theme'];
}

$modes=array(
    '' => '',
    'terms_conditions' => '{%terms_and_conditions%}',
    'text' => '{%general_text%}',
);

$main_config = array(
    'config[action_mode]' => array('type' => 'dropdownlist', 'items' => $modes, 'hint' => '{%mode%}'),
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('mobileevents'), 'hint' => '{%hint_article_action_theme%}'),
);

$theme_config = array();

if ( $theme == 'golfriend' ) {
    $theme_config = array(
        // alert
        'config[alertbox]' => array('type'=>'textarea', 'title'=>'%alertbox%'),

        // Configs
        'config[show_email]' => array('type'=>'checkbox'),

        'config[data_branch]' => array('type' => 'dropdownlist',
            'items' => CHtml::listData(Aegame::getBranches($this->gid),'id','title'),
            'hint' => '{%choose_branch%}'
        ),
        'config[user_profile_view]' => array('type' => 'dropdownlist',
            'items' => $actions,
            'hint' => '{%choose_branch%}'
        ),
        'config[place_view]' => array('type' => 'dropdownlist',
            'items' => $actions,
            'hint' => '{%choose_branch%}'
        ),
        'config[use_false_id]' => array('type'=>'checkbox','hint' => '{%allow_mixing_between_apps%}'),
        'config[debug_mode]' => array('type'=>'checkbox','hint' => '{%menu_for_submit_button%}'),
    );
} else if ( $theme == 'rentit' ) {

	if ( isset($model->config['action_mode']) AND $model->config['action_mode'] != 'text' ) {
		$theme_config = array(
			'config[title_agents]' => array('type'=>'text', 'title'=>'%hint_title_agents%'),
			'config[description_agents]' => array('type'=>'textarea', 'title'=>'%hint_description_agents%'),
			'config[title_landlords]' => array('type'=>'text', 'title'=>'%hint_title_landlords%'),
			'config[description_landlords]' => array('type'=>'textarea', 'title'=>'%hint_description_landlords%'),
			'config[title_tenants]' => array('type'=>'text', 'title'=>'%hint_title_tenants%'),
			'config[description_tenants]' => array('type'=>'textarea', 'title'=>'%hint_description_tenants%'),
		);
	} else {
		$theme_config = array(
			'config[description]' => array('type'=>'textarea', 'title'=>'%hint_description%'),
			'config[feedback_email]' => array('type'=>'text', 'title'=>'%hint_feedback_email%'),
			'config[feedback_subject]' => array('type'=>'text', 'title'=>'%hint_feedback_subject%'),
		);
	}

}

return array_merge( $main_config, $theme_config );