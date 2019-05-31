<?php

namespace packages\actionMitems\themes\demo\Components;

use Bootstrap\Components\BootstrapComponent;

trait Dashboard
{
    public $isLiked;

    public function getDashboard($params = array())
    {
        // $btns4[] = array('title' => '{#open_calender#}','onclick' => $this->getOnclickOpenSidemenu());
        // $btns4[] = array('title' => '{#close_sidemenu#}','onclick' => $this->getOnclickCloseSidemenu());
        $btns1 = array(array('title' => '{#enable_location#}','onclick' => $this->getOnclickLocation()));
        $btns2 = array(array('title' => '{#enable_notifications#}','onclick' => $this->getOnclickPushPermissions()));
        $btns3[] = array('title' => '{#login#}','onclick' => $this->getOnclickOpenAction('login'));
        $btns3[] = array('title' => '{#register#}','onclick' => $this->getOnclickOpenAction('registration',false,array(
            'sync_open' => 1,'id' => 'update_location'
        )));
//        $btns5 = array(array('title' => '{#login#}','onclick' => $this->getOnclickOpenAction('login')));
//        $btns6 = array(array('title' => '{#register#}','onclick' => $this->getOnclickOpenAction('registration',false,array('sync_open' => 1,'id' => 'update_location'))));

        if($this->model->getSavedVariable('system_source') == 'client_iphone'){
            $notification_title = '{#notifications_title_ios#}';
            $notification_text = '{#notifications_subtext_ios#}';
            $notifications = array('title' => $notification_title,'descrimitems_filter_labelption' => $notification_text,'icon' => 'intro_post.png','buttons' => $btns2);
        } else {
            $notification_title = '{#notifications_title_android#}';
            $notification_text = '{#notifications_subtext_android#}';
            $notifications = array('title' => $notification_title,'description' => $notification_text,'icon' => 'intro_post.png');
        }

        if($this->model->getSavedVariable('intro_shown') == 1){
            $content = array(
                array('title' => '{#welcome_title_login#}','description' => '{#welcome_subtext_login#}','icon' => 'intro_person.png','buttons' => $btns3),
                array('title' => '{#welcome_title_intro#}','description' => '{#welcome_subtext_intro#}','icon' => 'intro_shop.png'),
                array('title' => '{#welcome_title_location#}','description' => '{#welcome_subtext_location#}','icon' => 'intro_location.png','buttons' => $btns1),
                $notifications,
            );
        } else {

            if($this->model->sessionGet('logged_in') == 1){
                $content = array(

                    array('title' => '{#Dashboard#}','icon' => 'intro_shop.png'),
                );
                //$this->data->scroll[] = $this->getText('Hello World'),

            }else{
                $content = array(
                    array('title' => '{#welcome_to_Appzio#}','icon' => 'intro_shop.png'),
                    // array('title' => '{#welcome_to_My_First_App#}','description' => '{#welcome_to_Demo_App#}','icon' => 'intro_shop.png', 'buttons' => $btns4),
                    //  array('title' => '{#welcome_to_My_First_App_subtitle#}','description' => '{#welcome_to_Demo_App_subpage#}','icon' => 'intro_person.png'),
                    //  array('title' => '{#welcome_to_location#}','description' => '{#Select_your_Location#}','icon' => 'intro_location.png','buttons' => $btns1),
                    // $notifications,
                    array('title' => '{#welcome_to_Dashboard#}','icon' => 'intro_person.png','buttons' => $btns3),
                    // array('title' => '{#welcome_to_My_App_registration#}','icon' => 'intro_person.png','buttons' => $btns3),

                );
            }

        }

        return $this->uiKitIntroWithButtons($content);
    }
}