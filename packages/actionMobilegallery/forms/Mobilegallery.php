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

$buttons=array(
    'no_button' => '{%article_no_button%}',
    'submit' =>  '{%article_standard_complete_button%}',
    'save' =>  '{%article_save_button%}',
);

$mode=array(
    'gallery' => '{%gallery%}',
    'submit' =>  '{%submit%}',
    'entry' =>  '{%entry%}',
);

$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');
$menus = CHtml::listData(Aenavigation::model()->findAllByAttributes(array('app_id' => $this->gid)), 'id', 'name');
$variables = CHtml::listData(Aevariable::model()->findAllByAttributes(array('game_id' => $this->gid)), 'id', 'name');
$branches = CHtml::listData(Aebranch::model()->findAllByAttributes(array('game_id' => $this->gid)), 'id', 'name');

$config = Aeaction::getActionConfig($model->id);


$arr['config[subject]'] = array('type'=>'text', 'title'=>'%subject%');
$arr['config[msg]'] = array('type'=>'textarea', 'title'=>'%subject%');
$arr['config[button_action]'] = array('type'=>'dropdownlist','items' => $buttons, 'hint' => '{%hint_article_button_action%}');
$arr['config[mode]'] = array('type'=>'dropdownlist','items' => $mode, 'hint' => '{%hint_article_button_action%}');

if(isset($config->mode)) {
    switch ($config->mode) {
        case 'entry':
            $arr['config[gallery_id]'] = array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%hint_article_button_action%}');

            $arr['config[commenting]'] = array('type' => 'checkbox', 'hint' => '{%menu_for_submit_button%}');
            $arr['config[report]'] = array('type' => 'checkbox', 'hint' => '{%menu_for_submit_button%}');
            $arr['config[share]'] = array('type' => 'checkbox', 'hint' => '{%menu_for_submit_button%}');
            $arr['config[chat_content]'] = array('type' => 'textarea', 'title' => '%subject%');
            $arr['config[user]'] = array('type' => 'text', 'title' => '%user%');
            $arr['config[comment]'] = array('type' => 'textarea', 'title' => '%user%');
            $arr['config[rating]'] = array('type' => 'checkbox', 'title' => '{%rating_functionality%}');

            break;

        case 'submit':
            $arr['config[source_branch]'] = array('type' => 'dropdownlist', 'items' => $branches, 'hint' => '{%hint_article_button_action%}');
            $arr['config[gallery_id]'] = array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%hint_article_button_action%}');
            $arr['config[mainmenu_id]'] = array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%hint_article_button_action%}');
            break;

        case 'gallery';
            $arr['config[add_new_item]'] = array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%hint_article_button_action%}');
            $arr['config[gallery_item_id]'] = array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%hint_article_button_action%}');
            $arr['config[source_branch]'] = array('type' => 'dropdownlist', 'items' => $branches, 'hint' => '{%hint_article_button_action%}');
            $arr['config[gallery_id]'] = array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%hint_article_button_action%}');
            $arr['config[mainmenu_id]'] = array('type' => 'dropdownlist', 'items' => $actions, 'hint' => '{%hint_article_button_action%}');
            break;
    }
}

/*return array(
    'config[gallery_item_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_article_button_action%}'),
    'config[add_new_item]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_article_button_action%}'),
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[msg]' => array('type'=>'redactor', 'title'=>'%subject%'),
    'config[user]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[date]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[likes]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[add_meta]' => array('type'=>'textarea', 'title'=>'%cue_add_meta%'),
    'config[tags]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[user_images]' => array('type'=>'textarea', 'title'=>'%subject%'),
    'config[commenting]' => array('type'=>'checkbox', 'title'=>'%subject%'),
);*/

return $arr;