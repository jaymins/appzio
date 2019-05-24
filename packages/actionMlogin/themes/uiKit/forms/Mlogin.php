<?php

/**
 * This is a theme specific form control file, which works little differently from
 * the action's main config. Action's main config will always be included, but you
 * can override fields and add theme specific fields. They are shown only when the
 * correct theme is selected from the admin.
 *
 * NOTE: all fields are named config[yourfieldname], nothing else will work. You can input any
 * field names, and these are available inside the model using $this->getConfigField('fieldname')
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

$header = array(
    'header_no' => '{%no_header%}',
    'header_image' => '{%image_header%} (media > image2)',
    'header_tall_image' => '{%tall_image_header%} (media > image2)',
    'header_gradient' => '{%header_gradient%}',
    'header_plain' => '{%header_plain%}',
);

$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

$empty = array('0' => '');
$actions = $empty+$actions;

$config = array(
    '' => array('type' => 'divider', 'items' => $header, 'hint' => '{%hint_article_action_mode%}'),
    'config[header_mode]' => array('type' => 'dropdownlist', 'items' => $header, 'hint' => '{%hint_article_action_mode%}','onChange' => 'this.form.submit()'),
);

if(isset($model->config['header_mode']) AND $model->config['header_mode'] == 'header_gradient'){
        $config['config[gradient_top]'] = HtmlHelpers::actionPickColor( array( 'hint' => '{%hint_top_bar_color%}' ) );
        $config['config[gradient_bottom]'] = HtmlHelpers::actionPickColor( array( 'hint' => '{%hint_top_bar_color%}' ) );
}


$config['config[header_text]'] = array('type' => 'text', 'hint' => '{%header_text%}');
$config['config[header_logo]'] = array('type' => 'checkbox', 'hint' => 'Define the logo as article image 1 (under media)');
$config['config[terms_action]'] = array('type' => 'dropdownlist',
    'items' => $actions,
    'hint' => '{%choose_branch%}'
);



return $config;