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

$defaults = array(
    '' => '',
    'controller' => '{%individual_item%}',
    'publiclisting' => '{%public_listing%}',
    'create' => '{%create%}',
    'edit' => '{%edit%}',
    'liked' => '{%liked%}',
    'listing' => '{%listing%}',
    'location' => '{%location%}',
    'categories' => '{%category_selector%}',
    'filter' => '{%filter%}',
    'intro' => '{%intro%}',
    'reminder' => '{%reminder%}',
    'history' => '{%history%}',
    'adddevice' => '{%add_device%}',
    'alldevices' => 'All Devices',
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
    'config[location_items_update]' => array('type'=>'checkbox', "hint" => "Update item location with the user location"),
);

if ( isset($theme) AND $theme == 'electric' ) {
    $config['config[theme_layout]'] = array(
        'type' => 'dropdownlist',
        'items' => array(
            '' => '{%default%}',
            'tabs' => '{%tabs%}',
            'bottom_switcher' => '{%bottom_switcher%}',
        ),
        'hint' => '{%hint_theme_layout%}'
    );
}

return $config;