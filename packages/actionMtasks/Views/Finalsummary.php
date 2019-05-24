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

class Finalsummary extends BootstrapView {

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
        $this->model->rewriteActionConfigField('background_color', '#2BBB1C');
        //$this->model->rewriteActionConfigField('pull_to_refresh', 0);

        $close = $this->getOnclickRoute('Controller/reset/');
        $close[] = $this->getOnclickGoHome();
        $top[] = $this->getComponentImage('close-icon-div.png',array('onclick' => $close),
            array('floating' => 1,'float' => 'right','margin' => '5 5 0 0', "width" => 20));
        $header[] = $this->getComponentRow($top);
        $header[] = $this->getComponentImage('earnster_logo.png',array(),array('margin' => '0 60 -40 60'));

        $header[] = $this->components->getIntroText(
            '{#get_ready_to_earn#}!',
            '{#get_ready_to_earn_subtext#}'
        );

        $this->layout->scroll[] = $this->getComponentColumn($header,array(),array('background-color' => '#EFEFEF'));

        $taskdata = $this->getData('task', 'mixed');

        $barData['proofs_required'] = $taskdata->deadline - $taskdata->start_time;
        $barData['proofs_required'] = ceil($barData['proofs_required'] / $taskdata->repeat_frequency*$taskdata->times_frequency);
        $barData['proofcount'] = 0;
        $this->layout->scroll[] = $this->components->getLargeProgress($barData);

        /* box 1 -- cart */
        $box1[] = $this->components->getCartHeader($this->data);

        $cart = $this->getData('cart', 'array');

        foreach($cart as $product){
            $box1[] = $this->components->getProductListItem($product,array('no_controls' => true));
        }

        $this->layout->scroll[] = $this->components->getSummaryBox('{#what#}',$box1,false);

        /* box 2 -- adult */
        $adults = $this->getData('adults', 'array');
        $box2 = array();

        if($adults){
            foreach($adults as $adult){
                $box2[] = $this->components->getAdultRowSummary($adult);
            }
        }

        $this->layout->scroll[] = $this->components->getSummaryBox('{#who#}',$box2,false);

        /* box 3 -- summary */
        $box3[] = $this->components->getTaskSummary($this->data);

        $this->layout->scroll[] = $this->components->getSummaryBox('{#how#}',$box3,false);
        $this->layout->footer[] = $this->components->getComponentText('{#earn_more#}',array('style' => 'earnster_bottom_button','onclick' => $close,'uppercase' => true));
        return $this->layout;

    }

    public function getDivs(){

        $onclick[] = $this->getOnclickSubmit('Controller/cancel');
        $onclick[] = $this->getOnclickHideDiv('cancel_div');
        $onclick[] = $this->getOnclickSubmit('Controller/phase1');

        $divs['cancel_div'] = $this->components->getComponentConfirmationDialog(
            $onclick,'cancel_div','{#are_you_sure_you_want_to_discard_this_chore#}?');
        return $divs;
    }




    /* if view has getDivs defined, it will include all the needed divs for the view */


}
