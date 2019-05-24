<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');

class ArticleviewController extends ArticleController {

    public function getData(){
    	
    	if ( !isset($this->configobj->layout_config) OR empty($this->configobj->layout_config) ) {
    		return false;
    	}

        return $this->configobj->layout_config;
    }

}