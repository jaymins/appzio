<?php

namespace packages\actionMswipematch\themes\matchswapp\Views;

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
        $params['subtext'] = '{#you_and#} '.$name .' {#like_each_other#}.';

        $params['button1_text'] = '{#continue_browsing#}';
        $params['button1_onclick'] = $this->getOnclickSubmit('infinite/default/browse');

        $this->layout->scroll[] = $this->uiKitUserMatch($params);

        return $this->layout;
    }

    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();

        return $divs;
    }

}