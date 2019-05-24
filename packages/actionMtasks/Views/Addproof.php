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

class Addproof extends BootstrapView {

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
            'mode' => 'route_back','btn_title'=>false,'title' => '{#add_proof#}','route_back' => 'Tasklist/Default/'));

        /* reject note */
        $reject_note = $this->getData('reject_note', 'string');

        if($reject_note){
            $box4[] = $this->getComponentText($reject_note, array('style' => 'ern_reject_note'));
            $this->layout->scroll[] = $this->components->getSummaryBox('{#latest_reject_note_from_your_adult#}',$box4);
        }

        /* box 1 -- cart */
        $box1[] = $this->components->getCartHeader($this->data);

        $cart = $this->getData('cart', 'array');

        foreach($cart as $product){
            $box1[] = $this->components->getProductListItem($product,array('no_controls' => true));
        }
        
        $data = $this->getData('task_data', 'array');

        if(isset($data['nickname'])){
            $nickname = $data['nickname'];
        } elseif(isset($data['username'])){
            $nickname = $data['username'];
        } else {
            $nickname = '{#unknown#}';
        }

        $tasks = $this->getData('tasks_info', 'array');
        $this->layout->header[] = $this->components->getLargeProgress($tasks);

        $this->layout->scroll[] = $this->components->getSummaryBox('{#towards#}',$box1);

        /* Note */
        $choreType[] = $this->components->getComponentText(
            '{#' . $tasks['category_name'] . '#}',
            array(),
            array('margin' => '5 15 5 15'
            )
        );

        $this->layout->scroll[] = $this->components->getSummaryBox('{#chore_type#}',$choreType);

        /* box 3 -- summary */
        $box3[] = $this->components->getTaskSummary($this->data);
        $this->layout->scroll[] = $this->components->getSummaryBox('{#how#}',$box3);

        $box5[] = $this->getComponentText($tasks['comments'],array('style' => 'mtask_summary_header_content'));
        $this->layout->scroll[] = $this->components->getSummaryBox('{#parent_comment_to_deal#}',$box5);


        /* image placeholder */
        $image = $this->model->getSavedVariable('proofimage') ? $this->model->getSavedVariable('proofimage') : 'invisible-divider.png';
        $this->layout->scroll[] = $this->getComponentImage($image, array(
            'variable' => 'proofimage'
        ));

        /* add photo button */
        $this->layout->scroll[] = $this->getComponentText('{#add_a_photo#}', array('style' => 'steps_hint','uppercase' => true));
        $this->layout->scroll[] = $this->getComponentText('{#add_a_photo#}',array('style' => 'attach_image_btn',
            'onclick' => $this->getOnclickImageUpload('proofimage',array('sync_upload' => 1,'max_dimensions' => '450'))));

        /* date */
/*        $this->layout->scroll[] = $this->components->getHintedCalendar(
                    '{#date#}',
                    'proof_date',
                    time(),
                    array('header' => false)
                );*/

        /* submit to */
        //$this->layout->scroll[] = $this->getComponentText('{#submit_to#}', array('style' => 'steps_hint','uppercase' => true));
//        $this->layout->scroll[] = $this->getComponentText($nickname, array('style' => 'proof_field_static'));

        /* description */
        $this->layout->scroll[] = $this->components->getHintedField(
            '{#description#}',
            'description',
            'textarea'
        );

        $this->layout->scroll[] = $this->getComponentSpacer('30');




        $onclick = $this->components->getOnclickShowDiv('cancel_div',
            array('background' => 'blur','tap_to_close' => true),
            array('left' => '50','right' => '50','bottom' => $this->screen_height/2 - 200));
        $btns[] = $this->components->getComponentText('{#cancel#}',array('style' => 'mtask_cancel_btn','onclick' => $onclick,'uppercase' => true));

        $onclick_save = $this->components->getOnclickSubmit('mytasks/saveproof/'.$data['taskid']);
        $btns[] = $this->components->getComponentText('{#send#}',array('style' => 'mtask_send_btn_half','onclick' => $onclick_save,'uppercase' => true));



        $this->layout->footer[] = $this->getComponentRow($btns);
        return $this->layout;

    }

    public function getDivs(){

        $onclick[] = $this->getOnclickSubmit('mytasks/default');
        $onclick[] = $this->getOnclickHideDiv('cancel_div');

        $divs['cancel_div'] = $this->components->getComponentConfirmationDialog(
            $onclick,'cancel_div','{#are_you_sure_you_want_to_discard_this_proof#}?');
        return $divs;
    }




    /* if view has getDivs defined, it will include all the needed divs for the view */


}
