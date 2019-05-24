<?php

namespace packages\actionMnotifications\themes\uikit\Views;
use packages\actionMnotifications\Views\View as BootstrapView;
use packages\actionMnotifications\themes\uikit\Components\Components;

class View extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $notifications = $this->getData('notifications', 'array');
        $this->layout->header[] = $this->getComponentText('',[],['height' => 2,'background-color' => '#e5e5e5','width' => '100%']);

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
