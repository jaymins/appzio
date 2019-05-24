<?php

namespace packages\actionMnexudus\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Rules extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();

        $this->layout->header[] = $this->components->getBigNexudusHeader('{#rule_book#}');

        $col[] = $this->getComponentText('{#please_do#}',['style' => 'nexudus_rulebook_title','uppercase' => true],[]);

        $col[] = $this->setRule('{#keep_the_door_closed#}', 'icon-nexudus-close.png');
        $col[] = $this->setRule('{#take_your_phone_rule#}', 'icon-nexudus-rule-phone.png');
        $col[] = $this->setRule('{#take_all_the_rubbish#}', 'icon-nexudus-rubbish.png');
        $col[] = $this->setRule('{#leave_on_time_rule#}', 'icon-nexudus-rule-time.png');
        $col[] = $this->setRule('{#leave_feedback#}', 'icon-nexudus-rule-door.png');

        $col[] = $this->getComponentDivider();
        $col[] = $this->getComponentText('{#please_dont#}',['style' => 'nexudus_rulebook_title','uppercase' => true],[]);

        $col[] = $this->setRule('{#smoke_inside_the_booth#}', 'icon-nexudus-nosmoke.png');
        $col[] = $this->setRule('{#use_illigal_substances#}', 'icon-nexudus-hand.png');
        $col[] = $this->setRule('{#eat_hot_food#}', 'icon-nexudus-forks.png');
        $col[] = $this->setRule('{#bring_pets#}', 'icon-nexudus-pawn.png');
        //$col[] = $this->setRule('{#close_the_door_when_leaving_rule#}', 'icon-nexudus-rule-door.png');

        $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '0 15 0 15']);

        return $this->layout;
    }

    public function getDivs(){
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }

    public function setRule($text,$icon){
        $col[] = $this->getComponentImage($icon,[],['margin' => '15 15 15 15','width' => '30']);
        $col[] = $this->getComponentText($text,['style' => 'nexudus_uikit_formheader_left']);
        return $this->getComponentRow($col,[],['margin' => '0 0 0 0']);

    }



}
