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

$menus = CHtml::listData(Aenavigation::model()->findAllByAttributes(array('app_id' => $this->gid)), 'id', 'name');
$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

$modes = array(
    'recipe' =>  '{%mobile_recipe%}',
    'listing' => '{%mobile_listing%}',
    'categories' => '{%mobile_recipe_categories%}',
    'preparation' => '{%mobile_recipe_preparation%}',
    'shopping_list' => '{%shopping_list%}'
);

return array(
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing('recipe', true), 'hint' => '{%hint_article_action_theme%}'),
    'config[action_mode]' => array('type'=>'dropdownlist','items' => $modes, 'hint' => '{%hint_action_mode%}'),
    'config[recipe_listing_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_recipe_listing_id%}'),
    'config[recipe_item_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_recipe_item_id%}'),
    'config[gallery_item_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_article_button_action%}'),
    'config[preparation_info]' => array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%detail_view_for_recipe_preparation%}'),

    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[author]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[time]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[points_1]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[points_2]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[bookmarks]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[intro]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[step1]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[step2]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[step3]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[step4]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[step5]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[step6]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[step7]' => array('type'=>'textarea', 'title'=>'%subject%'),

    'config[servings]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient1]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient1_count]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient2]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient2_count]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient3]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient3_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient4]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient4_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient5]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient5_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient6]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient6_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient7]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient7_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient8]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient8_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient9]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient9_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient10]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient10_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient11]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient11_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient12]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient12_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient13]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient13_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient14]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient14_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[ingredient15]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[ingredient15_count]' => array('type'=>'text', 'title'=>'%subject%'),

    'config[chat_content]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[user_images]' => array('type'=>'textarea', 'title'=>'%subject%'),

    'config[publish_day]' => array('type'=>'text', 'title'=>'%subject%'),
);