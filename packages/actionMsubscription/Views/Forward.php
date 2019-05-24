<?php

namespace packages\actionMsubscription\Views;

class Forward extends View {

    /* @var \packages\actionMsubscription\Components\Components */
    public $components;
    public $theme;

    /**
     * Pagetwo constructor.
     * @param $obj
     */
    public function __construct($obj){
        parent::__construct($obj);
    }

    /**
     * @return \stdClass
     */
    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->getComponentFullPageLoader(array(),array('text-align'=>'center'));
        $this->layout->onload[] = $this->getOnclickSubmit('main');
        $this->layout->onload[] = $this->getOnclickOpenAction('cart');

        if($this->model->getSavedVariable('tempurl')){
            $this->layout->onload[] = $this->getOnclickOpenUrl($this->model->getSavedVariable('tempurl'));
        }

        return $this->layout;
    }


}
