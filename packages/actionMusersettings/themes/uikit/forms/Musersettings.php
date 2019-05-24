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
Yii::import('aelogic.packages.actionMquiz.Models.*');
$records = QuizModelDB::model()->findAllByAttributes(array('app_id' => $this->gid));
$empty = array('0' => '');
$fieldsets = $empty + CHtml::listData($records,'id','name');

$config = array(
    'config[mreg_delete_user]' => array('type' => 'checkbox', 'hint' => '{%hint_article_action_mode%}'),
    'config[mreg_collect_age]' => array('type' => 'checkbox', 'hint' => '{%hint_article_action_mode%}'),
    'config[hide_profile_progress]' => array('type' => 'checkbox', 'hint' => '{%hint_article_action_mode%}'),
);


    $config['config[no_profiletoggle]'] = array('type' => 'checkbox', 'hint' => 'Hide checkbox for toggling the user profile on or off');
    $config['config[no_likescounttoggle]'] = array('type' => 'checkbox', 'hint' => 'Hide checkbox for toggling the like counts');


    $config['config[settings_fields_1_title]'] = array('type' => 'text', 'title' => '%subject%');

    $config['config[settings_fields_1]'] = array('type' => 'dropdownlist',
        'items' => $fieldsets,
        'onChange' => 'this.form.submit()',
        'hint' => '{%choose_quiz%}'
    );

    if (isset($model->config['settings_fields_1']) AND $model->config['settings_fields_1']) {
        $config['config[settings_fields_2_title]'] = array('type' => 'text', 'title' => '%subject%');

        $config['config[settings_fields_2]'] = array('type' => 'dropdownlist',
            'items' => $fieldsets,
            'onChange' => 'this.form.submit()',
            'hint' => '{%choose_quiz%}'
        );
    }

    if (isset($model->config['settings_fields_2']) AND $model->config['settings_fields_2']) {

        $config['config[settings_fields_3_title]'] = array('type' => 'text', 'title' => '%subject%');

        $config['config[settings_fields_3]'] = array('type' => 'dropdownlist',
            'items' => $fieldsets,
            'onChange' => 'this.form.submit()',
            'hint' => '{%choose_quiz%}'
        );
    }

    if (isset($model->config['settings_fields_3']) AND $model->config['settings_fields_3']) {

        $config['config[settings_fields_4_title]'] = array('type' => 'text', 'title' => '%subject%');

        $config['config[settings_fields_4]'] = array('type' => 'dropdownlist',
            'items' => $fieldsets,
            'hint' => '{%choose_quiz%}'
        );

    }


return $config;