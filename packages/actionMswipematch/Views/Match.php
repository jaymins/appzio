<?php

namespace packages\actionMswipematch\Views;

use Bootstrap\Views\BootstrapView;

class Match extends BootstrapView {

    /* @var \packages\actionMswipematch\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj) {
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();

        $user = $this->getData('user', 'mixed');

        if(!isset($user['play_id'])){
            $this->layout->scroll[] = $this->getComponentText('{#user_not_found#}. {#sorry#}!', array(), array(
                'margin' => '80 40 10 40',
                'text-align' => 'center',
            ));
        }

        $params['left_image'] = $this->model->getSavedVariable('profilepic');

        if(isset($user['vars']['profilepic'])){
            $params['right_image'] = $user['vars']['profilepic'];
        }

        $name = isset($user['vars']['firstname']) ? $user['vars']['firstname'] : '{#anonymous#}';

        $params['title'] = '{#its_a_match#}!';
        $params['subtext'] = '{#you_and#} '.$name .' {#like_each_other#}. {#why_dont_you_start_chatting#}?';

        $params['button1_text'] = '{#chat#}';
        $params['button1_onclick'] = $this->getOnclickOpenAction('chat',false,[
            'id' => $this->model->getTwoWayChatId($this->model->playid,$user['play_id']),
            'sync_open' => 1,'back_button' => 1,'viewport' => 'bottom'
        ]);

        $params['button2_text'] = '{#continue_browsing#}';
        $params['button2_onclick'] = $this->getOnclickSubmit('browse');

        $this->layout->scroll[] = $this->uiKitUserMatch($params);

        return $this->layout;
    }

    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();

        return $divs;
    }

}