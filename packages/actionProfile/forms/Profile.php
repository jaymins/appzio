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

Something evil happened around here, sorry about that.

*/

$zero = array('joku' => array('type' => '0', 'name' => '{%select_field_type%}'));
$fieldtypes = CHtml::listData($zero + Controller::getFieldTypes(),'type', 'name');

$static = array(
    'dont' => array('id' => '0', 'name' => '{%dont_save_to_variable%}'),
    'create' => array('id' => 'createVar', 'name' => '{%create_variable%}')
);

$static2 = array(
    'dont' => array('id' => '0', 'name' => '{%dont_save_to_variable%}'),
    'create' => array('id' => 'createVar', 'name' => '{%create_variable%}')
);

$timezones = CHtml::listData(Controller::getTimeZones_forforms(), 'gmtOffset','name');
$variables = CHtml::listData($static + Aegame::getVariables($this->gid),'id','name');

$arr = array(
    'config[subject]' => array('type'=>'text', 'hint' => '{%action_msg_subject%}'),
    'config[msg]' => array('type'=>'redactor', 'hint' => '{%action_msg_body%}'),
    'config[shortmsg]' => array('type'=>'textarea','rows'=>5, 'width' => '100%', 'maxlength' => 200, 'hint' => '{%shortmsg_hint%}'),

    'config[profile_email]' => array('type'=>'checkbox', 'hint' => '{%profilecollector_email_hint%}'),
    'config[profile_password]' => array('type'=>'checkbox', 'hint' => '{%profilecollector_password_hint%}'),
    'config[profile_phone]' => array('type'=>'checkbox', 'hint' => '{%profilecollector_phone_hint%}'),
    'config[profile_timezone]' => array('type'=>'checkbox', 'hint' => '{%profilecollector_timezone_hint%}'),
    'config[default_timezone]' => array('type'=>'dropdownlist', 'items' => $timezones, 'hint' => '{%profilecollector_default_gmt%}'),

    'config[facebook_login]' => array('type' => 'checkbox', 'hint'=>'%pr_login_facebook%'),
    'config[twitter_login]' => array('type' => 'checkbox', 'hint'=>'%pr_login_twitter%'),

    'config[divider]' => array('type' => 'divider', 'htmlOptions' => array('class' => '{%variable%} 1'))
);

    $arr = handleVarGroup('variable1',$arr,$model,$this->gid,'#4c9e3f',$fieldtypes,$variables);

    $fieldtype = 'variable1_fieldtype';
    if(isset($model->config[$fieldtype]) AND strlen($model->config[$fieldtype]) > 1){
        $arr = handleVarGroup('variable2',$arr,$model,$this->gid,'#326899',$fieldtypes,$variables);
    }

    $fieldtype = 'variable2_fieldtype';
    if(isset($model->config[$fieldtype]) AND strlen($model->config[$fieldtype]) > 1){
        $arr = handleVarGroup('variable3',$arr,$model,$this->gid,'#cdb43d',$fieldtypes,$variables);
    }

    $fieldtype = 'variable3_fieldtype';
    if(isset($model->config[$fieldtype]) AND strlen($model->config[$fieldtype]) > 1){
        $arr = handleVarGroup('variable4',$arr,$model,$this->gid,'#cd32cf',$fieldtypes,$variables);
    }

    $fieldtype = 'variable4_fieldtype';
    if(isset($model->config[$fieldtype]) AND strlen($model->config[$fieldtype]) > 1){
        $arr = handleVarGroup('variable5',$arr,$model,$this->gid,'#000',$fieldtypes,$variables);
    }
    $fieldtype = 'variable5_fieldtype';
    if(isset($model->config[$fieldtype]) AND strlen($model->config[$fieldtype]) > 1){
        $arr = handleVarGroup('variable6',$arr,$model,$this->gid,'#fff',$fieldtypes,$variables);
    }    

return $arr;



function returnErrorSelector($name,$gid,$static,$variables){

    $name = 'config[' .$name .']';

    $arr[$name] = array('type' => 'dropdownlist',
        'items' => $variables,
        'onChange' => 'this.form.submit()',
        'hint' => '{%error_duplicate_variable%}',
        'style' => 'border: 2px dotted red'
    );

    return $arr;
}


/* check that this is not the same as the first one, if its, throw an error
    todo: make this work for all the fields properly ...
*/

/* this will add the required fields for adding a variable collector */
function handleVarGroup($name,$arr,$model,$gid,$color,$fieldtypes,$variables){

    $static = array(
        'dont' => array('id' => '0', 'name' => '{%dont_save_to_variable%}'),
        'create' => array('id' => 'createVar', 'name' => '{%create_variable%}'),
    );

    // how you like em' copy pastin?

    /* check that variable 2 doesn't have the same variable set as the 1 */
    if($name == 'variable2'){
        if(isset($model->config['variable2']) AND isset($model->config['variable1'])){
            if($model->config['variable2'] == $model->config['variable1']){
                return $arr + returnErrorSelector('variable2',$gid,$static,$variables);
            }
        }
    }

    /* check that variable 3 doesn't have the same variable set as the 1 */
    if($name == 'variable3'){
        if(isset($model->config['variable3']) AND isset($model->config['variable2'])){
            if($model->config['variable3'] == $model->config['variable2']){
                return $arr + returnErrorSelector('variable3',$gid,$static,$variables);
            }
        }

        if(isset($model->config['variable3']) AND isset($model->config['variable1'])){
            if($model->config['variable3'] == $model->config['variable1']){
                return $arr + returnErrorSelector('variable3',$gid,$static,$variables);
            }
        }

    }

    /* check that variable 3 doesn't have the same variable set as the 1 */
    if($name == 'variable4'){
        if(isset($model->config['variable4']) AND isset($model->config['variable3'])){
            if($model->config['variable4'] == $model->config['variable3']){
                return $arr + returnErrorSelector('variable4',$gid,$static,$variables);
            }
        }

        if(isset($model->config['variable4']) AND isset($model->config['variable2'])){
            if($model->config['variable4'] == $model->config['variable2']){
                return $arr + returnErrorSelector('variable4',$gid,$static,$variables);
            }
        }

        if(isset($model->config['variable4']) AND isset($model->config['variable1'])){
            if($model->config['variable4'] == $model->config['variable1']){
                return $arr + returnErrorSelector('variable4',$gid,$static,$variables);
            }
        }
    }

    /* check that variable 5 doesn't have the same variable set as the 1 */
    if($name == 'variable5'){
        if(isset($model->config['variable5']) AND isset($model->config['variable4'])){
            if($model->config['variable5'] == $model->config['variable4']){
                return $arr + returnErrorSelector('variable5',$gid,$static,$variables);
            }
        }

        if(isset($model->config['variable5']) AND isset($model->config['variable3'])){
            if($model->config['variable5'] == $model->config['variable3']){
                return $arr + returnErrorSelector('variable5',$gid,$static,$variables);
            }
        }

        if(isset($model->config['variable5']) AND isset($model->config['variable2'])){
            if($model->config['variable5'] == $model->config['variable2']){
                return $arr + returnErrorSelector('variable5',$gid,$static,$variables);
            }
        }

        if(isset($model->config['variable5']) AND isset($model->config['variable1'])){
            if($model->config['variable5'] == $model->config['variable1']){
                return $arr + returnErrorSelector('variable5',$gid,$static,$variables);
            }
        }
    }

    if($name == 'variable6'){
        if(isset($model->config['variable6']) AND isset($model->config['variable5'])){
            if($model->config['variable6'] == $model->config['variable5']){
                return $arr + returnErrorSelector('variable6',$gid,$static,$variables);
            }
        }

        if(isset($model->config['variable6']) AND isset($model->config['variable4'])){
            if($model->config['variable6'] == $model->config['variable4']){
                return $arr + returnErrorSelector('variable6',$gid,$static,$variables);
            }
        }

        if(isset($model->config['variable6']) AND isset($model->config['variable3'])){
            if($model->config['variable6'] == $model->config['variable3']){
                return $arr + returnErrorSelector('variable6',$gid,$static,$variables);
            }
        }

        if(isset($model->config['variable6']) AND isset($model->config['variable2'])){
            if($model->config['variable6'] == $model->config['variable2']){
                return $arr + returnErrorSelector('variable6',$gid,$static,$variables);
            }
        }

        if(isset($model->config['variable6']) AND isset($model->config['variable1'])){
            if($model->config['variable6'] == $model->config['variable1']){
                return $arr + returnErrorSelector('variable6',$gid,$static,$variables);
            }
        }
    }


    /* step one, show the selector */
    $pointer = 'config[' .$name .']';

    $arr[$pointer] = HtmlHelpers::VarField($gid,'{%collect_to_variable_hint%}',$name,$name .'_editing','border: 2px solid ' .$color .';');


    /* step two, show the field type selector */
    if(isset($model->config[$name]) AND $model->config[$name] > 0){
        $pointer = 'config[' .$name .'_fieldtype]';
        $arr[$pointer] = array('type' => 'dropdownlist',
            'items' => $fieldtypes,
            'onChange' => 'this.form.submit()',
            'hint' => '{%variable_field_type_hint%}',
            'style' => 'border: 2px solid ' .$color .';'
        );
    }

    /* step three, show the title field */
    $fieldtype = $name .'_fieldtype';

    if(isset($model->config[$name]) AND $model->config[$name] > 0 AND isset($model->config[$fieldtype]) AND strlen($model->config[$fieldtype]) > 1){
        $fieldtitle = 'config[' .$name .'_title]';
        $arr[$fieldtitle] = array('type'=>'text', 'hint' => '{%hint_variable_title%}', 'style' => 'border: 2px solid ' .$color .';');
    }

    /* step four, show the list field if required */
    if(isset($model->config[$name]) AND $model->config[$name] > 0 AND isset($model->config[$fieldtype]) AND ($model->config[$fieldtype] == 'dropdownlist'
            OR $model->config[$fieldtype] == 'radiolistinline'
            OR $model->config[$fieldtype] == 'checkboxlist'
        )){

        $fieldtitle = 'config[' .$name .'_list]';
        $arr[$fieldtitle] = array('type'=>'text', 'hint' => '{%hint_variable_list%}', 'style' => 'border: 2px solid ' .$color .';');
    }

    return $arr;
}