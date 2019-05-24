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

class Detailspopup extends Details {

    /* @var \packages\actionMtasks\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();

        $onclick[] = $this->components->getOnclickSubmit('controller/save/dosave');
        $this->layout->header[] = $this->components->getFauxTopbar(array(
            'mode' => 'close','btn_title'=>'{#save#}','title' => '{#describe_chore#}',
            'btn_onclick' => $onclick, 'popup' => 1));
        $this->layout->header[] = $this->getComponentSpacer('12');
        
        $taskinfo = $this->getData('taskinfo', 'mixed');

        if($this->model->validation_errors){
            $error = isset($this->model->validation_errors['general_error']) ? $this->model->validation_errors['general_error'] : '{#please_check_your_entry#}';

            $this->layout->scroll[] = $this->getComponentText($error,
                array('style' => 'detail_title_error','onclick' => $this->getOnclickShowElement('header')));
        }

        if($taskinfo['comments']){
            $this->layout->scroll[] = $this->getComponentText('{#task_comment_from_adult#}',array('style' => 'ern_task_comment_title','uppercase' => true));
            $this->layout->scroll[] = $this->getComponentDivider();
            $this->layout->scroll[] = $this->getComponentText($taskinfo['comments'],array('style' => 'ern_task_comment'));
        }

        $this->setForm();
        $this->layout->scroll[] = $this->getComponentDivider();

        //$onclick[] = $this->getOnclickClosePopup();

        $this->layout->footer[] = $this->components->getComponentText('{#save#}',array('style' => 'earnster_bottom_button','onclick' => $onclick,'uppercase' => true));

        return $this->layout;

    }




    /* if view has getDivs defined, it will include all the needed divs for the view */


}
