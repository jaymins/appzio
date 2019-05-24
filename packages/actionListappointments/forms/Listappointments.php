<?php

Yii::import('application.modules.aelogic.article.components.*');

$actions = CHtml::listData(Aeaction::getAllActions($this->gid),'id','name');

return array(
	'config[subject]' => array('type'=>'text', 'title'=>'%subject%'),
    'config[chat_action_id]' => array('type'=>'dropdownlist','items' => $actions, 'hint' => '{%hint_chat_action_id%}'),
);