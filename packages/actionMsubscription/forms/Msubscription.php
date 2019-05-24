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


use packages\actionMsubscription\Models\PurchaseProductsModel;

$actions = CHtml::listData(Aeaction::getAllActions($this->gid), 'id', 'name');
$shortname = 'mregister';

if (isset($model->type_id) AND $model->type_id > 0) {
    $type = Aeactiontypes::model()->findByPk($model->type_id);
    if (isset($type->shortname)) {
        $shortname = $type->shortname;
    }
}

Yii::import('aelogic.packages.actionMsubscription.Models.*');
$products = PurchaseProductsLegacy::model()->findAllByAttributes(['app_id' => $this->gid]);

$arr = array(
    'config[subject]' => array('type' => 'text', 'title' => '%subject%'),
    'config[article_action_theme]' => array('type' => 'dropdownlist', 'items' => Controller::getActionThemeListing($shortname), 'hint' => '{%hint_article_action_theme%}'),
    'config[data_mode]' => array('type' => 'dropdownlist', 'items' => [
        'form' => 'Input in this form',
        'db' => 'Use products from the database'
    ], 'hint' => 'Choose how the products data is defined'),


    'config[subscription_terms]' => array('type' => 'dropdownlist',
        'items' => $actions,
        'hint' => '{%choose_action_that_holds_terms_and_conditions%}'
    ),

    'config[subscription_affiliation_code]' => array('type' => 'checkbox'),
);

$arr['config[header_text]'] = array('type' => 'redactor', 'title' => '%subject%');
$arr['config[footer_text]'] = array('type' => 'redactor', 'title' => '%subject%');
$arr['config[header_subscription_active]'] = array('type' => 'redactor', 'title' => '%subject%');


if(isset($model->config['data_mode']) AND $model->config['data_mode'] == 'db'){
    foreach ($products as $product){
        if(isset($product->code) AND $product->code){
            $name = 'product_'.$product->id;
            $arr['config['.$name.']'] = array('type' => 'checkbox', 'hint' => 'Enable product '.$product->name);
        }
    }
} else {
    $arr['config[subscription_price]'] = array('type' => 'text', 'title' => '%subject%');
    $arr['config[subscription_code_ios]'] = array('type' => 'text', 'title' => '%subject%');
    $arr['config[subscription_code_android]'] = array('type' => 'text', 'title' => '%subject%');
    $arr['config[monthly_subscription_price]'] = array('type' => 'text', 'title' => ';subject%');
    $arr['config[monthly_subscription_code_ios]'] = array('type' => 'text', 'title' => '%subject%');
    $arr['config[monthly_subscription_code_android]'] = array('type' => 'text', 'title' => '%subject%');
    $arr['config[annual_subscription_price]'] = array('type' => 'text', 'title' => ';subject%');
    $arr['config[annual_subscription_code_ios]'] = array('type' => 'text', 'title' => '%subject%');
    $arr['config[annual_subscription_code_android]'] = array('type' => 'text', 'title' => '%subject%');
}

return $arr;