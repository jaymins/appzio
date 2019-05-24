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

class Summary extends BootstrapView {

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

        $onclickX = $this->components->getOnclickShowDiv('cancel_div',
            array('background' => 'blur','tap_to_close' => true),
            array('left' => '50','right' => '50','bottom' => $this->screen_height/2 - 200));
        $this->layout->header[] = $this->components->getFauxTopbar(array(
            'mode' => 'gohome','btn_title'=>'X',
            'title' => '{#review_deal#}','route_back' => 'Controller/phase3/',
            'btn_onclick' => $onclickX));
        $this->layout->header[] = $this->components->getProgressHeader(4);
        $this->layout->header[] = $this->getComponentSpacer('12');
        $this->layout->scroll[] = $this->getComponentDivider();

        /* box 1 -- cart */
        $box1[] = $this->components->getCartHeader($this->data);

        $cart = $this->getData('cart', 'array');

        foreach($cart as $product){
            $box1[] = $this->components->getProductListItem($product,array('no_controls' => true));
        }

        $onclick = $this->getOnclickOpenAction(
            'cartpopup',
            false,
            array('sync_open' => 1,'open_popup' => 1,'sync_close' => 1),
            'Cart/default',
            true);
        $this->layout->scroll[] = $this->components->getSummaryBox('{#what#}',$box1,false,$onclick);

        /* box 2 -- adult */
        $adults = $this->getData('adults', 'array');
        $box2 = array();

        if($adults){
            foreach($adults as $adult){
                $box2[] = $this->components->getAdultRowSummary($adult);
            }
        }

        $onclick = $this->getOnclickOpenAction(
            'taskdetailspopup',
            false,
            array('sync_open' => 1,'open_popup' => 1,'sync_close' => 1,'id' => 'popup_adults'));
        $this->layout->scroll[] = $this->components->getSummaryBox('{#who#}',$box2,false,$onclick);

        /* box 3 -- summary */
        $box3[] = $this->components->getTaskSummary($this->data);

        $onclick = $this->getOnclickOpenAction(
            'taskdetailspopup',
            false,
            array('sync_open' => 1,'open_popup' => 1,'sync_close' => 1,'id' => 'popup_details'));

        $this->layout->scroll[] = $this->components->getSummaryBox('{#how#}',$box3,false,$onclick);

        $onclick_save = $this->components->getOnclickSubmit('Controller/phase5/');
        $btns[] = $this->components->getComponentText('{#send#}',array('style' => 'earnster_bottom_button','onclick' => $onclick_save,'uppercase' => true));

        $taskdata = $this->getData('task', 'mixed');
        $barData['proofs_required'] = $taskdata->deadline - $taskdata->start_time;
        $barData['proofs_required'] = ceil($barData['proofs_required'] / $taskdata->repeat_frequency*$taskdata->times_frequency);
        $barData['proofcount'] = 0;

        $this->layout->footer[] = $this->components->getLargeProgress($barData);

        $this->layout->footer[] = $this->getComponentRow($btns);
        return $this->layout;

    }

    public function getDivs(){

        $onclick[] = $this->getOnclickSubmit('Controller/cancel');
        $onclick[] = $this->getOnclickHideDiv('cancel_div');
        $onclick[] = $this->getOnclickOpenAction('home');

        $divs['cancel_div'] = $this->components->getComponentConfirmationDialog(
            $onclick,'cancel_div','{#are_you_sure_you_want_to_discard_this_chore#}?');
        return $divs;
    }




    /* if view has getDivs defined, it will include all the needed divs for the view */


}
