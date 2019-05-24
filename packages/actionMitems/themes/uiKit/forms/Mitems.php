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

$mode = array(
    '' => '',
    'intro' => '{%intro%}',
    'publiclisting' => '{%public_listing%}',
    'controller' => '{%individual_item%}',
    'listing' => '{%listing%}',
    'allitems' => '{%list_all_items%}',
    'events' => '{%events%}',
    'addevent' => '{%add_event%}',
    'editevent' => '{%edit_event%}',
    'createvisit' => '{%create_visit%}',
    'editvisit' => '{%edit_visit%}',
    'createnote' => '{%create_note%}',
    'editnote' => '{%edit_note%}',
    'imagepreview' => '{%image_preview%}',
    'statistics' => '{%statistics_latest%}',
    'statisticstop' => '{%statistics_top_5%}',
);

$config = array(
    'config[mode]' => array('type' => 'dropdownlist', 'items' => $mode, 'hint' => '{%hint_article_action_mode%}'),
);

return $config;