<?php

namespace packages\actionMtasks\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMtasks\Controllers\Components;
use stdClass;
use function stristr;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Adultlist extends BootstrapView {

    /* @var \packages\actionMtasks\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1($tab=1){
        return $this->phase1($tab);
    }

    public function phase1($tab=1){
        $this->layout = new \stdClass();
        $this->model->rewriteActionConfigField('hide_menubar', 1);
        $parameters['title'] = '{#choose_adult#}';

        $this->layout->header[] = $this->components->getFauxTopbar(array('mode' => 'gohome','btn_title'=>false));
        $this->layout->header[] = $this->components->getProgressHeader();

        $adults = $this->getData('adults', 'array');
        $count = 0;

        if($adults){
            foreach($adults as $adult){
                $this->layout->scroll[] = $this->components->getAdultRow($adult,count($adults),$count,$tab);
                $count++;
            }
        }

        if(empty($adults) OR !$adults){
            $this->layout->scroll[] = $this->components->getIntroText(
                '{#add_an_adult#}',
                '{#add_an_adult_text_1#}'
            );
        }

        $onclick = $this->components->getOnclickTab(2,array('id' => 'new'));
        $this->layout->scroll[] = $this->components->getAddAdultButton($onclick);


        if($this->getData('error', 'string')){
            $this->layout->footer[] = $this->getComponentText($this->getData('error', 'string'),array('style' => 'mtasks_footer_error'));
        }

        $onclick = $this->getOnclickSubmit('controller/phase2/');
        $text = strtoupper($this->model->localize('{#continue#}'));
        $this->layout->footer[] = $this->getComponentText($text,array('style' => 'earnster_bottom_button','onclick' => $onclick));

        return $this->layout;
    }

    public function tab2(){
        $layout = new stdClass();
        $this->model->rewriteActionConfigField('hide_menubar', 1);
        
        $done = $this->getData('done', 'bool');

        if($this->model->getMenuId() != 'new' OR $done){
            return $this->tab1(2);
        }

        $layout->header[] = $this->components->getFauxTopbar(array('btn_title'=>'{#add#}'));
        $page[] = $this->components->getAddAdult();

        $layout->scroll[] = $this->getComponentColumn($page,array(),array('width' => $this->screen_width,
            'background-color' => '#EFEFEF'));

        return $layout;
    }



    /* if view has getDivs defined, it will include all the needed divs for the view */


}
