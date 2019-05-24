<?php

namespace Bootstrap\Components\AppzioUiKit\Listing;
use Bootstrap\Views\BootstrapView;

trait uiKitPeopleListWithLikes
{

    public function uiKitPeopleListWithLikes(array $content, array $parameters=array(),array $styles=array()) {

        $rows = array();

        foreach($content as $user){
            $rows[] = $this->getUserRowListingWithLikes($user,$parameters);
        }

        if ($rows) {
            return $this->getComponentColumn($rows);
        }

        return $this->getComponentText('{#no_users_found_at_the_moment#}', array('style' => 'jm_notification_text'));
    }

    public function getUserRowListingWithLikes($user,$parameters){

        if(!isset($user['play_id'])){
            return $this->getComponentText('');
        }

        $tab = isset($parameters['tab']) ? $parameters['tab'] : 1;
        $chat_info = isset($parameters['chat_info']) ? $parameters['chat_info'] : false;

        $id = $user['play_id'];
        $profilepic = isset($user['profilepic']) ? $user['profilepic'] : 'anonymous-person.png';

        if(isset($parameters['extra_icon']) AND isset($user['instagram_username']) AND $user['instagram_username']) {
            $text_column_width = $this->screen_width - 250;
        } else {
            $text_column_width = $this->screen_width - 100;
        }

        $firstname = $this->getNickname($user);

        $row[] = $this->getComponentImage($profilepic,[
            'style' => 'uikit_ukp_profilepic',
            'imgwidth' => '400',
            'imgheight' => '400',
            'imgcrop' => 'yes',
            'format' => 'jpg',
            'priority' => '9',
            'onclick' => $this->uiKitOpenProfile($id)],[]);

        $style = $chat_info ? 'uikit_ukp_name_small' : 'uikit_ukp_name';

        $name[] = $this->getComponentText($firstname,[
            'style' => $style,
            'onclick' => $this->uiKitOpenProfile($id)]);
        
        if($chat_info) {
            if(isset($user['chat']['msg']) AND isset($user['chat']['msgtime']) AND $user['chat']['msg'] AND $user['chat']['msgtime']){
                $time = strtotime($user['chat']['msgtime']);
                $date = date('n/j G:i',$time);
                $string = $date .' — ' .wordwrap($user['chat']['msg']);
                $name[] = $this->getComponentText($string,[],['font-size' => '12','margin' => '0 0 0 20','color' => '#B2B4B3']);
            }
        }

        $row[] = $this->getComponentColumn($name,[],['width' => $text_column_width]);

        if(isset($parameters['extra_icon']) AND isset($user['instagram_username']) AND $user['instagram_username']){
            if($parameters['instaclick_command']){
                $onclick[] = $this->getOnclickSubmit($parameters['instaclick_command'].$id);
            }

            $onclick[] = $this->getOnclickOpenUrl('https://instagram.com/'.$user['instagram_username']);
            $row[] = $this->getComponentText('{#follow#}',[
                'style' => 'uikit_list_follow_button_small',
                'onclick' => $onclick]);
        }


        $icons = array();

        if(isset($parameters['icon_chat']) AND isset($parameters['play_id'])){
            $click = $this->getOnclickOpenAction('chat',false,[
                'id' => $this->model->getTwoWayChatId($parameters['play_id'],$id),
                'sync_open' => 1,'back_button' => 1,'viewport' => 'bottom'
            ]);


            $icons[] = $this->getComponentImage($parameters['icon_chat'],['style' => 'uikit_ukp_iconpic','onclick' => $click]);

            if($chat_info){
                if(isset($user['unread']) AND $user['unread'] > 0){
                    $icons[] = $this->getComponentText('',[],[
                        'width' => 10,'height' => '10',
                        'background-color' => 'FF2718',
                        'border-radius' => '5',
                        'margin' => '0 5 5 -5',
                        'vertical-align' => 'middle']);
                }
            }


        }

        if(isset($parameters['icon_dont_like']) AND $parameters['like_route']){
            $click = $this->getOnclickSubmit($parameters['like_route'].'unlike/'.$user['play_id']);
            $icons[] = $this->getComponentImage($parameters['icon_dont_like'],['style' => 'uikit_ukp_iconpic','onclick' => $click]);
        }

        if(isset($parameters['icon_like'])){
            if(!isset($user['is_liked']) OR !$user['is_liked']){
                $click = $this->getOnclickSubmit($parameters['like_route'].'like/'.$user['play_id']);
                $icons[] = $this->getComponentImage($parameters['icon_like'],['style' => 'uikit_ukp_iconpic','onclick' => $click],[]);
            }
        }

        if(isset($parameters['icon_bookmark']) AND isset($parameters['icon_bookmark_active']) AND $parameters['bookmark_route']){
            $like[] = $this->getOnclickHideElement('unliked'.$id.$tab,['transition' => 'none']);
            $like[] = $this->getOnclickShowElement('liked'.$id.$tab,['transition' => 'none']);
            $like[] = $this->getOnclickSubmit($parameters['bookmark_route'].'bookmark/'.$id,['loader_off' => true]);

            $unlike[] = $this->getOnclickHideElement('liked'.$id.$tab,['transition' => 'none']);
            $unlike[] = $this->getOnclickShowElement('unliked'.$id.$tab,['transition' => 'none']);
            $unlike[] = $this->getOnclickSubmit($parameters['bookmark_route'].'removebookmark/'.$id,['loader_off' => true]);

            if(isset($user['is_bookmarked']) AND $user['is_bookmarked']){
                $icons[] = $this->getComponentImage($parameters['icon_bookmark'],[
                    'style' => 'uikit_ukp_iconpic','onclick' => $like,'id' => 'unliked'.$id.$tab,'visibility' => 'hidden']);
                $icons[] = $this->getComponentImage($parameters['icon_bookmark_active'],[
                    'style' => 'uikit_ukp_iconpic','onclick' => $unlike,'id' => 'liked'.$id.$tab]);
            } else {
                $icons[] = $this->getComponentImage($parameters['icon_bookmark'],[
                    'style' => 'uikit_ukp_iconpic','onclick' => $like,'id' => 'unliked'.$id.$tab]);
                $icons[] = $this->getComponentImage($parameters['icon_bookmark_active'],[
                    'style' => 'uikit_ukp_iconpic','onclick' => $unlike,'id' => 'liked'.$id.$tab,'visibility' => 'hidden']);
            }

        } elseif(isset($parameters['icon_bookmark'])){
            $icons[] = $this->getComponentImage($parameters['icon_bookmark'],['style' => 'uikit_ukp_iconpic']);
        }


        $row[] = $this->getComponentRow($icons,[],['float' => 'right','floating' => '1', 'text-align' => 'right']);

        $col[] = $this->getComponentRow($row,[],['padding' => '20 20 10 20','width' => '100%']);
        $col[] = $this->getComponentDivider();

        return $this->getComponentColumn($col,['filter' => strtolower($firstname)],[]);

    }

}