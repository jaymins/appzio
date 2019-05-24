<?php

namespace packages\actionMnexudus\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Intro extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();

        $mode = $this->getData('mode', 'string');
        $this->layout->header[] = $this->components->getBigNexudusHeader('{#how_it_works#}');

        $pic1 = $this->model->getConfigParam('actionimage1') ? $this->model->getConfigParam('actionimage1') : 'intro1.png';
        $pic2 = $this->model->getConfigParam('actionimage2') ? $this->model->getConfigParam('actionimage2') : 'intro3.png';


        $col[] = $this->getComponentImage($pic1,[],[
            'height' => $this->screen_height-350,'margin' => '0 0 0 0']);
        
        $swipe[] = $this->getComponentColumn($col,[],['margin' => '0 0 0 0','text-align' => 'center']);

        unset($col);

        $col[] = $this->getComponentImage($pic2,[],[
            'height' => $this->screen_height-350,'margin' => '0 0 0 0']);

        $swipe[] = $this->getComponentColumn($col,[],['margin' => '0 0 0 0','text-align' => 'center']);

        $this->layout->scroll[] = $this->getComponentSwipe($swipe,[],[
            'margin' => '10 30 10 30','text-align' => 'center',
            'height' => $this->screen_height-350]);



        $this->layout->footer[] = $this->getComponentSwipeAreaNavigation('#ffffff','#8B8484');
        $this->layout->footer[] = $this->getComponentSpacer(15);
        if($mode == 'gohome'){
            $this->layout->footer[] = $this->uiKitButtonFilled('{#home#}',[
                'onclick' => $this->getOnclickOpenAction('home')
            ]);
        } else {
            $this->layout->footer[] = $this->uiKitButtonFilled('{#got_it_lets_start#}',[
                'onclick' => $this->getOnclickSubmit('finish')
            ]);
        }

        $this->layout->footer[] =$this->getComponentSpacer(20);

        return $this->layout;
    }
    



}
