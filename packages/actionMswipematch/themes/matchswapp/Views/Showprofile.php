<?php

namespace packages\actionMswipematch\themes\matchswapp\Views;

use Bootstrap\Views\BootstrapView;

class Showprofile extends \packages\actionMswipematch\Views\Showprofile
{

    /* @var \packages\actionMswipematch\themes\matchswapp\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function buttons($id,$userdata){
        if(isset($userdata['matching']) AND $userdata['matching']){

            $click = $this->getOnclickOpenAction('chat',false,[
                'id' => $this->model->getTwoWayChatId($this->model->playid,$id),
                'back_button' => 1,'sync_open' => 1,'viewport' => 'bottom'
            ]);

            if(isset($userdata['bookmark']) AND $userdata['bookmark']) {
                $onclick[] = $this->getOnclickSubmit('controller/removebookmark/'.$id);
                $onclick[] = $this->getOnclickSubmit($id);
                $this->layout->footer[] = $this->uiKitButtonHollow('{#remove_bookmark#}',[
                    'onclick' => $onclick
                ]);
            } else {
                $onclick[] = $this->getOnclickSubmit('controller/bookmark/'.$id);
                $onclick[] = $this->getOnclickSubmit($id);

                $this->layout->footer[] = $this->uiKitButtonHollow('{#bookmark#}',
                    ['onclick' => $onclick]);
            }

            $this->layout->footer[] = $this->getComponentSpacer(20);
            $this->layout->footer[] = $this->uiKitButtonFilled('{#chat#}',
                ['onclick' => $click]);
            $this->layout->footer[] = $this->getComponentSpacer(10);


        } else {
            $params['id'] = $id;

            if(isset($userdata['bookmark']) AND $userdata['bookmark']){
                $params['is_bookmarked'] = true;
            }

            if(isset($userdata['liked']) AND $userdata['liked']){
                $params['is_liked'] = true;
            }

            $params['right_click'][] = $this->getOnclickSubmit('Showprofile/like/'.$id);
            $params['right_click'][] = $this->getOnclickGoHome();

            $params['left_click'][] = $this->getOnclickSubmit('Showprofile/unlike/'.$id);
            $params['left_click'][] = $this->getOnclickGoHome();

            $this->layout->footer[] = $this->uiKitUserSwiperControls($params);
            $this->layout->footer[] = $this->getComponentSpacer(10);
        }



    }


}