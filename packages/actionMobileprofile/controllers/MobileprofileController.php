<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.controllers.*');
Yii::import('application.modules.aelogic.article.controllers.*');
Yii::import('application.modules.aelogic.packages.actionMobileprofile.models.*');

class MobileprofileController extends ArticleController {

    public $taboutput;
    public $debug = false;
    public $jones = array('1','2');

    public $tabsmenu_images = array(
        '1' => array('tab-profile-1.png','25%'),
        '2' => array('tab-profile-2.png','25%'),
        '3' => array('tab-profile-3.png','25%'),
        '4' => array('tab-profile-4.png','25%'));

    public function tab1(){
        $data = new StdClass();
        $data->scroll = $this->getMainScroll();
        $data->footer[] = $this->getSpacer('10');
        //$data->footer[] = $this->getFacebookRegisterOrInvite();
        $data->footer[] = $this->getSpacer('10');
        return $data;
    }

    public function tab2(){
        $data = new StdClass();
        $data->scroll = $this->shoppinglist(array());
        return $data;
    }

    public function tab3(){
        $data = new StdClass();
        $data->scroll = $this->getNotifications(array());
        return $data;
    }

    public function tab4(){
        $data = new StdClass();
        $data->scroll = $this->getBookmarks(array());
        return $data;
    }

    private function shoppinglist($output,$nothing=false){

        if($this->menuid == 667){
            $this->flushCacheTab(2);
        }

        if($this->menuid == 517){
            AeplayVariable::deleteWithName($this->playid,'shopping_list',$this->gid);
            $this->flushCacheTab(2);
        } elseif($nothing == false) {
            if (isset($this->varcontent['shopping_list']) AND $this->varcontent['shopping_list']) {
                $items = json_decode($this->varcontent['shopping_list']);
                $arr = (array)$items;

                if (is_object($items) AND !empty($arr)) {
                    $output[] = $this->getMenu('shoppinglist_top_menu', array('style' => 'shoppinglist_menu'));
                    $shoppinglist = $this->moduleShoppinglist('shopping_list',array());
                    $output = array_merge($output,$shoppinglist);
                    return $output;
                }
            }
        }

        $output = $this->getRefresh('Nothing in the shopping list yet.

You can add recipes to shopping list
from the daily recipes');

        return $output;

    }

    private function getNotifications($output){

        $textstyle = array('font-size' => 13,'width' => '70%',
            'text-color'=> '#474747',
            'vertical-align' => 'middle'
        );

        $imgstyle = array('imgcrop' => 'round', 'width' => '15%',
            'imgwidth' => '400','imgheight' => '400'
            );

        if(isset($this->varcontent['notifications']) AND $this->varcontent['notifications']){
            $notifications = (array)json_decode($this->varcontent['notifications']);
            $notifications = array_reverse($notifications);


            foreach($notifications as $key => $notification){

                $notification = (array)$notification;

                $userid = $notification['user_id'];
                $actionid = $notification['action_id'];
                $action = $notification['action'];

                $act = Aeaction::model()->findByPk($actionid);
                if(is_object($act)){
                    $conf = json_decode($act->config);
                    $subject = $conf->subject;
                    $foodpic = $conf->image_portrait;

                    $userinfo = $this->getUserVariables($userid);



                    if(isset($notification['action_id'])){
                        $imgstyle['action'] = 'open-action';
                        $imgstyle['margin'] = '10 10 10 10';
                        $imgstyle['config'] = $actionid;
                    }


                    $pic = isset($userinfo['profilepic'])?$userinfo['profilepic']:'anonymous2.png';

                    $columns[] = $this->getImagebutton($pic,3123123123,false,$imgstyle);

                    /*              $imgstyle['margin'] = '10 10 10 0';
                                    $columns[] = $this->getImagebutton($foodpic,3123123124,false,$imgstyle);*/

                    if($action == 'bookmarked'){
                        $columns[] = $this->getText($userinfo['name'] .' ' .$action .' ' .$subject,$textstyle);
                    } elseif($action == 'requested_recipe'){
                        $columns[] = $this->getText($userinfo['name'] .' ' .$action .' ' .$subject .' to be turned into a recipe',$textstyle);
                    } elseif($action == 'liked'){
                        $columns[] = $this->getText($userinfo['name'] .' ' .$action .' ' .$subject .' ',$textstyle);
                    } else {
                        $columns[] = $this->getText($userinfo['name'] .' ' .$action .' ' .$subject,$textstyle);
                    }

                    $output[] = $this->getRow($columns,array(
                        'height' => '45',
                        'vertical-align' => 'middle'
                        /*                    'orientation' => 'vertical','width' => '73%',
                                            'vertical-align' => 'top','margin' => '7 0 0 0',
                                            'background_color' => '#f4f4f4','radius' => '4',
                                            'align' => 'left',
                                            'padding' => '0 0 0 0'*/
                    ));
                    unset($columns);
                }

            }

        } else {
            $output[] = $this->articlemenuobj->getSingleImageMenuItem('667','refresh_menu','refresh.png');

            $obj = new StdClass;
            $obj->type = 'msg-plain';
            $obj->style = 'chat_load_more';
            $obj->content = 'No new notifications.

Upload something to get noticed!';
            $output[] = $obj;

        }

      //  print_r($output);die();


        return $output;
    }

    private function getBookmarks($output){

        $bookmarks = $this->moduleBookmarking('list');

        if(empty($bookmarks)){
            $output[] = $this->articlemenuobj->getSingleImageMenuItem('668','refresh_menu','refresh.png');

            $obj = new StdClass;
            $obj->type = 'msg-plain';
            $obj->style = 'recipe_chat_nocomments';
            $obj->content = 'No bookmarks.

You can bookmark items from recipes!';
            $output[] = $obj;
        } else {
            $output[] = $this->getMenu('bookmarks_top_menu',array('style'=>'bookmarks_top_menu'));
            $output = array_merge($output,$bookmarks);
        }

        return $output;
    }


    private function getMainScroll($output=array(),$validate=false){

        $output[] = $this->getImage('featured_image',array(
            'width' => '100%','variable' => true,
            'defaultimage' => 'submit-hint.jpg'));

        $output[] = $this->getImagebutton('add-photo-button.png',77533,false,array('style'=>'profile_add_menu'
        ,'action' => 'open-branch','config' => $this->getConfigParam('submit_image_branch')));
        //$output[] = $this->getImagebutton('add-photo-button.png','201','heart_on.png',array('style' => 'profile_add_menu'));

        $haspoints = false;
        $points = 0;

        while($var = each($this->varcontent)){
            $key = $var['key'];
            $value = $var['value'];

            if(strstr($key,'points_')){
                $name = ucfirst(str_replace('points_','',$key));
                $num = $value / 10;
                $haspoints = true;
                $points++;

                if($value < 11){
                    $level = 1;
                } elseif($value > 10) {
                    $level = 2;
                } else {
                    $level = 'Master';
                }

                $obj = new StdClass;
                $obj->type = 'progress';
                $obj->content = $num;
                $obj->text_content = $name .', Level ' .$level;
                $obj->progress_image = $this->getImageFileName('progress-fill.png');
                $obj->track_color = '#FFFFFF';
                $obj->style = 'progress_style1';
                if($key != 'points_activity'){
                    $output2[] = $obj;
                }
            }
        }

        $obj = new StdClass;
        $obj->type = 'progress';
        $obj->content = $points / 30;
        $obj->text_content = 'Cookify Chef, Level 1';
        $obj->progress_image = $this->getImageFileName('progress-fill.png');
        $obj->track_color = '#FFFFFF';
        $obj->style = 'progress_style1';
        $output[] = $obj;

        if(isset($output2)){
            $output = mergeArray($output,$output2);
        }

        if($haspoints == false){
            $obj = new StdClass;
            $obj->type = 'progress';
            $obj->content = '0';
            $obj->text_content = 'Cook your first dish to unlock more';
            $obj->progress_image = $this->getImageFileName('progress-fill.png');
            $obj->track_color = '#FFFFFF';
            $obj->style = 'progress_style1';
            $output[] = $obj;
        }



        return $output;
    }

}

?>