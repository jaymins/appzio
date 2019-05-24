<?php

/**
 * This is the admin interface configuration for the action. All configuration options
 * are easily available under model as $this->getConfigParam('param_name');
 */

$shortname = basename(__FILE__);
$shortname = str_replace('.php', '', $shortname);
$shortname = strtolower($shortname);

if(isset($model->type_id) AND $model->type_id > 0){
    $type = Aeactiontypes::model()->findByPk($model->type_id);
    if(isset($type->shortname)){
        $shortname = $type->shortname;
    }
}

$mode = array(
    '' => '{%default%}',
    'listing' => '{%listing%}',
    'create' => '{%create%}',
    'userbid' => '{%userbid%}'
);

$config = array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[article_action_theme]' => array('type'=>'dropdownlist','items' => Controller::getActionThemeListing($shortname), 'hint' => '{%hint_article_action_theme%}'),
    'config[mode]' => array('type' => 'dropdownlist', 'items' => $mode, 'hint' => '{%hint_article_action_mode%}'),
);

return $config;