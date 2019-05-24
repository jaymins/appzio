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

class Details extends BootstrapView {

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
        $this->model->rewriteActionConfigField('subject','{#enter_details#}');

        $this->setHeader();
        $this->setForm();

        $this->layout->scroll[] = $this->getComponentDivider();

        $onclick = $this->components->getOnclickRoute('Controller/phase3/dosave',true);
        $this->layout->footer[] = $this->components->getComponentText('{#continue#}',array('style' => 'earnster_bottom_button','onclick' => $onclick,'uppercase' => true));

        return $this->layout;

    }

    public function setHeader()
    {
        $this->layout->header[] = $this->components->getFauxTopbar(array(
            'mode' => 'route_back', 'btn_title' => false, 'title' => '{#details#}', 'route_back' => 'Controller/phase2/'));
        $this->layout->header[] = $this->components->getProgressHeader(3);
        $this->layout->header[] = $this->getComponentSpacer('12');

        $this->layout->scroll[] = $this->getComponentText('{#youve_chosen#} ' . $this->getData('task_type', 'string'),
            array('style' => 'detail_title', 'onclick' => $this->getOnclickShowElement('header')));

        if ($this->model->validation_errors) {
            $error = isset($this->model->validation_errors['general_error']) ? $this->model->validation_errors['general_error'] : '{#please_check_your_entry#}';

            $this->layout->scroll[] = $this->getComponentText($error,
                array('style' => 'detail_title_error', 'onclick' => $this->getOnclickShowElement('header')));
        } elseif ($this->model->sessionGet('isEditing')) {
            $taskinfo = $this->getData('taskinfo', 'mixed');
            $this->layout->scroll[] = $this->getComponentText($taskinfo['comments'],
                array('style' => 'detail_title_error', 'onclick' => $this->getOnclickShowElement('header')));
        } else {
            $img[] = $this->getComponentImage($this->getData('task_icon', 'string'),array('style' => 'mtask_detail_image','onclick' => $this->getOnclickHideElement('header')));
            $this->layout->scroll[] = $this->getComponentRow($img,array('id' => 'header'),array('height'=>'150','text-align' => 'center','background-color' => '#ffffff', 'margin' => '0 20 10 20'));
        }
    }

    public function setForm(){
        $taskinfo = $this->getData('taskinfo', 'mixed');

        /* determine the values we should show */
        if($this->model->getSubmittedVariableByName('type')){
            $type = array('value' => $this->model->getSubmittedVariableByName('type'));
        }elseif(isset($taskinfo->title)){
            $type = array('value' => $taskinfo->title);
        } else {
            $type = array();
        }

        if($this->model->getSubmittedVariableByName('description')) {
            $description = array('value' => $this->model->getSubmittedVariableByName('description'));
        }elseif(isset($taskinfo->description)){
            $description = array('value' => $taskinfo->description);
        } else {
            $description = array();
        }

        if($this->model->getSubmittedVariableByName('repeat_frequency')) {
            $repeat_frequency = array('value' => $this->model->getSubmittedVariableByName('repeat_frequency'));
            $times = array('value' => $this->model->getSubmittedVariableByName('repeat_frequency'));
        }elseif(isset($taskinfo->repeat_frequency)){
            // todo: count the right value

            $hours = floor($taskinfo->repeat_frequency/3600);
            $days = $hours/24;
            $weeks = floor($days/7);
            $months = floor($weeks/4);

            if($months){
                $repeat = 'Month';
            } elseif($weeks){
                $repeat = 'Week';
            } elseif($days){
                $repeat = 'Day';
            } else {
                $repeat = 'Hours';
            }

            $repeat_frequency = array('value' => $repeat);
            $times = array('value' => $taskinfo->times_frequency.' x');

        } else {
            $repeat_frequency = array();
            $times = array();
        }

        if($this->model->getSubmittedVariableByName('starting_date')) {
            $start_time = $this->model->getSubmittedVariableByName('starting_date');
        }elseif(isset($taskinfo->start_time)){
            $start_time = $taskinfo->start_time;
        } else {
            $start_time = time();
        }


        if($this->model->getSubmittedVariableByName('ending_date')) {
            $deadline = $this->model->getSubmittedVariableByName('ending_date');
        }elseif(isset($taskinfo->deadline)){
            $deadline = $taskinfo->deadline;
        } else {
            $deadline = time() + 500000;
        }

        if($taskinfo['status'] != 'countered'){
            $subcol[] = $this->components->getHintedSelector(
                '{#choose_type#}',
                'type',
                $this->getData('task_type_list', 'string'),
                $type
            );
        }

        $descriptionHint = '{#describe_service#}';
        if($taskinfo['status'] == 'countered'){
            $descriptionHint = '{#describe_service_counter#}';
        }

        $subcol[] = $this->components->getHintedField(
            $descriptionHint,
            'description',
            'textarea',
            $description
        );

        $subcol[] = $this->components->getHintedSelectorComposit(
            '{#choose_how_often#}',
            array('times', 'how_often'),
            array(
                'how_often' => ';;Hour;Hour;Day;Day;Week;Week;2-Week;2-Week;Month;Month',
                'times' => ';;1 x;1 x;2 x;2 x;3 x;3 x;4 x;4 x;5 x;5 x;6 x;6 x'),
            array('how_often' => $repeat_frequency, 'times' => $times)
        );

        $subcol[] = $this->components->getHintedCalendar(
            '{#starting_date#}',
            'starting_date',
            $start_time,
            array('width' => $this->screen_width - 120)
        );

        $subcol[] = $this->components->getHintedCalendar(
            '{#ending_date#}',
            'ending_date',
            $deadline,
            array('width' => $this->screen_width - 120)
        );

        $subcol[] = $this->getComponentText('{#youll_need_to_share#}',array('style' => 'mtask_form_disclaimer'));
        $this->layout->scroll[] = $this->getComponentColumn($subcol,array(),array('background-color' => '#ffffff','margin' => '5 20 0 20'));

    }




    /* if view has getDivs defined, it will include all the needed divs for the view */


}
