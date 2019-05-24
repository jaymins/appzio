<?php

$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');
$shortname = 'mregister';

if(isset($model->type_id) AND $model->type_id > 0){
    $type = Aeactiontypes::model()->findByPk($model->type_id);
    if(isset($type->shortname)){
        $shortname = $type->shortname;
    }
}

return array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing($shortname), 'hint' => '{%hint_article_action_theme%}'),

    // alert
    'config[alertbox]' => array('type'=>'textarea', 'title'=>'%alertbox%'),

    'config[rate_url_android]' => array('type'=>'text', 'title'=>'%hint_rate_url_android%'),
    'config[rate_url_ios]' => array('type'=>'text', 'title'=>'%hint_rate_url_ios%'),
);