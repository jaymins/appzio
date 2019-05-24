<?php

namespace packages\actionMnotifications\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMnotifications\Controllers\Components;
use function stristr;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class View extends BootstrapView {

    /* @var \packages\actionMnotifications\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $notifications = $this->getData('notifications', 'array');

        if(!$notifications){

            $col[] = $this->getComponentImage('notification-bell-icon.png',[],['width' => '30%','margin' => '0 0 20 0']);

            if(!$this->model->getSavedVariable('system_push_id')) {
                $col[] = $this->getComponentText('{#please_enable_notifications_to_be_kept_up_to_date#}',[],['text-align' => 'center']);
            } else {
                $col[] = $this->getComponentText('{#no_active_notifications_at_the_moment#}',[],['text-align' => 'center']);
            }

            $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '40 60 40 60','text-align' => 'center']);
        }

        foreach($notifications as $notification){
            $this->layout->scroll[] = $this->components->getNotificationRow($notification);
        }

        if(!$this->model->getSavedVariable('system_push_id')){
            $this->layout->footer[] = $this->uiKitButtonHollow('{#enable_push_notifications#}',['onclick' => $this->getOnclickPushPermissions(['sync_open' => 1])]);
            $this->layout->footer[] = $this->getComponentSpacer('15');
        }


        return $this->layout;
    }





}
