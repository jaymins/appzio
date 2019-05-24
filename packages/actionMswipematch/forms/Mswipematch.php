<?php

/*

This is the admin interface configuration for the action. All configuration options
are easily available under model as $this->getConfigParam('param_name');

On this action we use the configuration to determine which fields are active & what
is the login branch. Login branch is important, because registration closes the login
in the end of the registration.

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
$shortname = 'mregister';

if(isset($model->type_id) AND $model->type_id > 0){
    $type = Aeactiontypes::model()->findByPk($model->type_id);
    if(isset($type->shortname)){
        $shortname = $type->shortname;
    }
}

$mode = array(
    '' => '',
    'mymatches' => '{%my_matches%}',
    'profile' => '{%profile%}',
    'swiper' => '{%swiper%}',
    'filtering' => '{%filter_preferences%}',
    'infinite' => '{%infinite_list%}',
    'showprofile' => '{%show_user_profile%}',
    'grid' => '{%grid%}',
    'checkin' => '{%check_in%}',
    'locationselector' => '{%locationselector%}',
);

$adsize = array(
    '' => '',
    'banner' => '{%small%} (320x50)',
    'large' => '{%large%} (320x100)',
    'rectangle' => '{%box%} (300x250)',
);

$colors = array(
    'black' => 'black',
    'white' => 'white',
);

Yii::import('aelogic.packages.actionMquiz.Models.*');
$records = QuizModelDB::model()->findAllByAttributes(array('app_id' => $this->gid));
$empty = array('0' => '');
$fieldsets = $empty + CHtml::listData($records,'id','name');


$config = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing($shortname), 'hint' => '{%hint_article_action_theme%}'),
    'config[mode]' => array('type' => 'dropdownlist', 'items' => $mode, 'hint' => '{%hint_article_action_mode%}'),
    'config[icon_colors]' => array('type' => 'dropdownlist', 'items' => $colors, 'hint' => '{%hint_article_action_mode%}'),
);

if ( isset($model->config['mode']) AND $model->config['mode'] == 'checkin' ) {
    $config['config[allow_free_location_selection]'] = array('type' => 'checkbox', 'hint' => "Allow user to set location freely");
    $config['config[update_user_location]'] = array('type' => 'checkbox', 'hint' => "Whether users's location should be updated when they check into the place");
    $config['config[predictive_location_input]'] = array('type' => 'checkbox', 'hint' => "Use a separate action to set users location with predictive Google text input");
    $config['config[max_radius]'] = array('type' => 'text', 'hint' => "Radius to search for a place in relation to users current location (in meters). Maximum 50000m.");
    $config['config[take_user_home]'] = array('type' => 'checkbox', 'hint' => "Open home action after check in");
    //$config['config[map_show_users_location]'] = array('type' => 'checkbox', 'hint' => "Whether to show location of user or last checked place");

}

if ( isset($model->config['mode']) AND $model->config['mode'] == 'infinite' ) {
    $config['config[banner_ad_id]'] = array('type' => 'text', 'hint' => "Set GoogleAds banner ID");
    $config['config[ad_threshold]'] = array('type' => 'text', 'hint' => "After how many users to show the banner");
    $config['config[ad_size]'] = array('type' => 'dropdownlist', 'items' => $adsize, 'hint' => "Banner size");

    $config['config[original_image_dimensions]'] = array('type' => 'checkbox', 'hint' => "Allow user to set location freely");
}

if ( isset($model->config['mode']) AND $model->config['mode'] == 'grid' ) {
    $config['config[banner_ad_id]'] = array('type' => 'text', 'hint' => "Set GoogleAds banner ID");
}


if ( isset($model->config['mode']) AND $model->config['mode'] == 'showprofile' ) {
    $config['config[enable_interstitials]'] = array('type' => 'checkbox', 'hint' => '{%show_interstitial_advertising%}');
    $config['config[interstitial_threshold]'] = array('type' => 'text', 'hint' => 'After how many profile views to show the advertising');

    $config['config[hide_like_count]'] = array('type'=>'checkbox', 'hint'=>'{%allow_resetting_all_matches%}');

    $config['config[settings_fields_1_title]'] = array('type' => 'text', 'hint' => 'Title for the box');
    $config['config[settings_fields_1_variables]'] = array('type' => 'textarea', 'hint' => 'Title;varname\nTitle;varname');

    $config['config[settings_fields_2_title]'] = array('type' => 'text', 'hint' => 'Title for the box');
    $config['config[settings_fields_2_variables]'] = array('type' => 'textarea', 'hint' => 'Title;varname\nTitle;varname');

    $config['config[settings_fields_3_title]'] = array('type' => 'text', 'hint' => 'Title for the box');
    $config['config[settings_fields_3_variables]'] = array('type' => 'textarea', 'hint' => 'Title;varname\nTitle;varname');

    $config['config[settings_fields_4_title]'] = array('type' => 'text', 'hint' => 'Title for the box');
    $config['config[settings_fields_4_variables]'] = array('type' => 'textarea', 'hint' => 'Title;varname\nTitle;varname');

    $config['config[settings_fields_5_title]'] = array('type' => 'text', 'hint' => 'Title for the box');
    $config['config[settings_fields_5_variables]'] = array('type' => 'textarea', 'hint' => 'Title;varname\nTitle;varname');

    $config['config[settings_fields_6_title]'] = array('type' => 'text', 'hint' => 'Title for the box');
    $config['config[settings_fields_6_variables]'] = array('type' => 'textarea', 'hint' => 'Title;varname\nTitle;varname');

    $config['config[settings_fields_7_title]'] = array('type' => 'text', 'hint' => 'Title for the box');
    $config['config[settings_fields_7_variables]'] = array('type' => 'textarea', 'hint' => 'Title;varname\nTitle;varname');

}

if ( isset($model->config['mode']) AND $model->config['mode'] == 'filtering' ) {
    $config['config[allow_reset]'] = array('type'=>'checkbox', 'hint'=>'{%allow_resetting_all_matches%}');
    $config['config[age_filtering]'] = array('type'=>'checkbox', 'title'=>'Allow filtering by age');
    $config['config[use_miles]'] = array('type'=>'checkbox', 'title'=>'Allow filtering by age');
    $config['config[min_age]'] = array('type'=>'text', 'title'=>'Age from');
    $config['config[max_age]'] = array('type'=>'text', 'title'=>'Age to');

    $config['config[distance_filtering]'] = array('type'=>'checkbox', 'title'=>'Distance filtering');
    $config['config[min_distance]'] = array('type'=>'text', 'title'=>'Distance from');
    $config['config[max_distance]'] = array('type'=>'text', 'title'=>'Distance to');

    $config['config[settings_fields_1_title]'] = array('type'=>'text', 'title'=>'%subject%');

    $config['config[settings_fields_1]'] = array('type' => 'dropdownlist',
        'items' => $fieldsets,
        'onChange' => 'this.form.submit()',
        'hint' => '{%choose_quiz%}'
    );

    if ( isset($model->config['settings_fields_1']) AND $model->config['settings_fields_1'] ) {
        $config['config[settings_fields_2_title]'] = array('type' => 'text', 'title' => '%subject%');

        $config['config[settings_fields_2]'] = array('type' => 'dropdownlist',
            'items' => $fieldsets,
            'onChange' => 'this.form.submit()',
            'hint' => '{%choose_quiz%}'
        );
    }

    if ( isset($model->config['settings_fields_2']) AND $model->config['settings_fields_2'] ) {

        $config['config[settings_fields_3_title]'] = array('type' => 'text', 'title' => '%subject%');

        $config['config[settings_fields_3]'] = array('type' => 'dropdownlist',
            'items' => $fieldsets,
            'onChange' => 'this.form.submit()',
            'hint' => '{%choose_quiz%}'
        );
    }

    if ( isset($model->config['settings_fields_3']) AND $model->config['settings_fields_3'] ) {

        $config['config[settings_fields_4_title]'] = array('type' => 'text', 'title' => '%subject%');

        $config['config[settings_fields_4]'] = array('type' => 'dropdownlist',
            'items' => $fieldsets,
            'hint' => '{%choose_quiz%}'
        );

    }

    if ( isset($model->config['settings_fields_4']) AND $model->config['settings_fields_4'] ) {

        $config['config[settings_fields_5_title]'] = array('type' => 'text', 'title' => '%subject%');

        $config['config[settings_fields_5]'] = array('type' => 'dropdownlist',
            'items' => $fieldsets,
            'hint' => '{%choose_quiz%}'
        );

    }

    if ( isset($model->config['settings_fields_5']) AND $model->config['settings_fields_5'] ) {

        $config['config[settings_fields_6_title]'] = array('type' => 'text', 'title' => '%subject%');

        $config['config[settings_fields_6]'] = array('type' => 'dropdownlist',
            'items' => $fieldsets,
            'hint' => '{%choose_quiz%}'
        );

    }
}


return $config;