<?php

Yii::import('application.modules.aelogic.article.components.*');

class matchswapp_Article_View_Text extends Article_View_Text {

    public $required_params = array();
    public $content;

	public function template() {

		$obj = new StdClass;
        $obj->type = 'msg-plain';
        $obj->content = $this->content;

        $params = array(
            'selected_state','variable'
        );

        foreach ($params as $param) {
            if ( isset($this->options[$param]) ) {
                $obj->$param  = $this->options[$param];
            }
        }

        return $obj;
	}

}