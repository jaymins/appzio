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

$config = array(
    'unset' => array(
        'config[alertbox]',
        'config[dont_require_email_code]',
        'config[mreg_collect_photo]',
        'config[mreg_collect_gender]',
        'config[mreg_collect_full_name]',
        'config[mreg_collect_nickname]',
        'config[mreg_collect_email]',
        'config[mreg_collect_password]',
        'config[mreg_collect_age]',
        'config[mreg_collect_birthdate]',
        'config[mreg_email_strict_validation]',
        'config[mreg_collect_phone]',
        'config[mreg_collect_profile_comment]',
        'config[mreg_hint_text_for_profile_comment]',
        'config[mreg_email_body]',
        'config[mreg_email_subject]',
        'config[mreg_social_facebook]',
        'config[mreg_social_twitter]',
        'config[mreg_social_google]',
        'config[mreg_social_instagram]',
        'config[mreg_collect_terms]',
        'config[mreg_terms_action]',
        'config[mreg_valid_domains]',
        'config[login_branch]',
        'config[logout_branch]',
        'config[intro_branch]',
        'config[intro_action]',
        'config[hide_header]',
        'config[debug_mode]',
    )
);

return $config;