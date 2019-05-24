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

class Editadult extends BootstrapView {

    /* @var \packages\actionMtasks\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $layout = new stdClass();
        $this->model->rewriteActionConfigField('hide_menubar', 1);
        $done = $this->getData('done', 'bool');

        $adult = $this->getData('adult', 'mixed');

        $layout->header[] = $this->components->getFauxTopbar(array(
            'btn_title'=>'{#save#}','route_back' => 'controller/phase1/',
            'btn_onclick' => $this->getOnclickRoute('controller/editadult/save_'.$adult->id)));
        $page[] = $this->components->getEditAdult(array(),$adult);

        $layout->scroll[] = $this->getComponentColumn($page,array(),array('width' => $this->screen_width,
            'background-color' => '#EFEFEF'));

        return $layout;
    }



    /* if view has getDivs defined, it will include all the needed divs for the view */


}
