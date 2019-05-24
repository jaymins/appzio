<?php

/**
 * This is the admin interface configuration for the action. All configuration options
 * are easily available under model as $this->getConfigParam('param_name');
 *
 * NOTE: all fields are named config[yourfieldname], nothing else will work. You can invent any
 * field names, these are available when rendering the view (under views/)
 *
 * IMPORTANT: make sure to define your action's shortname
 *
 *
 * supported field types, uses Yii Booster, this would be your best source of information:
 * http://www.yiiframework.com/forum/index.php/topic/36258-yiibooster/
 *
 * 'text' => 'textFieldRow',
 * 'password' => 'passwordFieldRow',
 * 'textarea' => 'textAreaRow',
 * 'file' => 'fileFieldRow',
 * 'radio' => 'radioButtonRow',
 * 'checkbox' => 'checkBoxRow',
 * 'listbox' => 'dropDownListRow',
 * 'dropdownlist' => 'dropDownListRow',
 * 'checkboxlist' => 'checkBoxListRow',
 * 'radiolist' => 'radioButtonListRow',
 *
 * //HTML5 types not supported in YiiBooster yet: render as textField
 * 'url' => 'textFieldRow',
 * 'email' => 'textFieldRow',
 * 'number' => 'textFieldRow',
 *
 * //'range'=>'activeRangeField', not supported yet
 * 'date' => 'datepickerRow',
 *
 * //new YiiBooster types
 * 'captcha' => 'captchaRow',
 * 'daterange' => 'dateRangeRow',
 * 'redactor' => 'redactorRow',
 * 'markdowneditor' => 'markdownEditorRow',
 * 'uneditable' => 'uneditableRow',
 * 'radiolistinline' => 'radioButtonListInlineRow',
 * 'checkboxlistinline' => 'checkBoxListInlineRow',
 * 'select2' => 'select2Row'
 *
 */

$shortname = basename(__FILE__);
$shortname = str_replace('.php', '', $shortname);
$shortname = strtolower($shortname);

if(isset($model->type_id) AND $model->type_id > 0){
	$type = Aeactiontypes::model()->findByPk($model->type_id);
	if(isset($type->shortname)){
		$shortname = $type->shortname;
	}
}

Yii::import('aelogic.packages.actionMquiz.Models.*');
$records = QuizModelDB::model()->findAllByAttributes(array('app_id' => $this->gid));
$empty = array('0' => '{%dynamic%}');
$fieldsets = $empty + CHtml::listData($records,'id','name');

$defaults = array(
    '' => '',
    'quizlist' => '{%list_of_quizzes%}',
    'questionlist' => '{%list_of_questions%}',
    'question' => '{%invidual_question%}',
    'questionflow' => '{%question_flow%}',
);

if ( isset($model->config['article_action_theme']) AND $model->config['article_action_theme'] ) {
    $theme = $model->config['article_action_theme'];
	$mode = Controller::getActionModes( $shortname, $model->config['article_action_theme'] );
    $mode = $defaults;
} else {
	$mode = $defaults;
}

$config = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing($shortname), 'hint' => '{%hint_article_action_theme%}'),
    'config[mode]' => array('type' => 'dropdownlist', 'items' => $mode, 'hint' => '{%hint_article_action_mode%}'),
);

if ( isset($model->config['mode']) ) {
    if($model->config['mode'] == 'questionlist' OR $model->config['mode'] == 'questionflow'){
        $records = QuizModelDB::model()->findAllByAttributes(array('app_id' => $this->gid));
        $config['config[quiz]'] = array('type' => 'dropdownlist',
            'items' => $fieldsets,
            'hint' => '{%choose_quiz%}'
        );
    }

    if($model->config['mode'] == 'questionflow') {
        $config['config[flow_progress]'] = array('type'=>'checkbox', 'hint'=>'Show progress bar');
        $config['config[flow_skip]'] = array('type'=>'checkbox', 'hint'=>'Allow skip');
        $config['config[flow_skip_filled]'] = array('type'=>'checkbox', 'hint'=>'Don\'t show questions where variable is already set');
        $config['config[flow_home]'] = array('type'=>'dropdownlist','items' => CHtml::listData(Aeaction::getAllActions($this->gid),'id','name'), 'hint' => 'Where to take use in the end?');
        $config['config[flow_complete_action]'] = array('type'=>'checkbox', 'hint'=>'Complete action once finished');
    }
}



return $config;