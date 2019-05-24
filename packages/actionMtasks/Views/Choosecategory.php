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

class Choosecategory extends BootstrapView {

    /* @var \packages\actionMtasks\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $this->model->rewriteActionConfigField('hide_menubar', 1);

        $this->layout->header[] = $this->components->getFauxTopbar(array(
            'mode' => 'gohome','btn_title'=>false,'title' => '{#choose_task#}','route_back' => 'Controller/phase1/'));
        $this->layout->header[] = $this->components->getProgressHeader(2);

        $this->layout->scroll[] = $this->getComponentSpacer('20');

        $row[] = $this->components->getTaskSelector('chores','{#chores#}','{#housework_and_cleaning#}','icon-task-chores.png');
        $row[] = $this->components->getTaskSelector('school','{#extra_school_work#}','{#extra_credit_for_class#}','icon-task-school.png');
        $this->layout->scroll[] = $this->getComponentRow($row);
        $this->layout->scroll[] = $this->getComponentSpacer('10');

        unset($row);
        $row[] = $this->components->getTaskSelector('community','{#community_service#}','{#volunteer_and_donate#}','icon-task-community.png');
        $row[] = $this->components->getTaskSelector('other','{#other#}','{#name_your_own_task#}','icon-task-other.png');
        $this->layout->scroll[] = $this->getComponentRow($row);

        $onclick = $this->getOnclickSubmit('controller/phase3/');
        $text = strtoupper($this->model->localize('{#continue#}'));
        $this->layout->footer[] = $this->getComponentText($text,array('style' => 'earnster_bottom_button','onclick' => $onclick));

        return $this->layout;

    }




    /* if view has getDivs defined, it will include all the needed divs for the view */


}
