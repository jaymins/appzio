<?php

namespace packages\actionMswipematch\Views;

use Bootstrap\Views\BootstrapView;

class Showprofile extends BootstrapView {

    /* @var \packages\actionMswipematch\themes\igers\Components\Components */
    public $components;
    public $theme;
    public $userdata;

    public function __construct($obj) {
        parent::__construct($obj);
    }


    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();

        $userdata = $this->getData('user_info', 'array');
        $this->userdata = $userdata;
        $id = $userdata['play_id'];
        $name = $this->getNickname($userdata['vars']);

        $this->layout->scroll[] = $this->uiKitFullWidthImageSwiper($userdata['images'],[
            'overlay' => $this->getBlockButton($id)
        ]);

        $col[] = $this->getComponentText($name,['style' => 'uikit_profileview_title']);

        if(isset($userdata['like_count']) AND $userdata['like_count'] AND !$this->model->getConfigParam('hide_like_count')){
            if(!isset($userdata['vars']['hide_like_count']) || $userdata['vars']['hide_like_count'] != 1){
                $col[] = $this->getComponentImage('insta_followers_icon.png',['style' => 'uikit_profileview_icon']);
                $col[] = $this->getComponentText($userdata['like_count'].' {#likes#}',['style' => 'uikit_profileview_smalltext']);
            }
        }

        if(isset($userdata['vars']['instagram_username'])){
            $onclick[] = $this->getOnclickSubmit('Controller/recordinstaclick/'.$id);
            $onclick[] = $this->getOnclickOpenUrl('https://instagram.com/'.$userdata['vars']['instagram_username']);
            $col[] = $this->getComponentImage('follow-instagram.png',[
                'style' => 'uikit_profileview_icon_right',
                'onclick' => $onclick]);
        }

        $this->layout->scroll[] = $this->getComponentRow($col,[],['vertical-align' => 'bottom','padding' => '10 15 10 15']);

        if($this->model->getConfigParam('settings_fields_1_title')){
            $this->configureBlocks($userdata['vars']);
        }elseif(isset($userdata['vars']['profile_comment']) AND $userdata['vars']['profile_comment']){
            $this->layout->scroll[] = $this->getComponentText($userdata['vars']['profile_comment'],['style' => 'uikit_profileview_bodytext']);
        } elseif(isset($userdata['vars']['instagram_bio']) AND $userdata['vars']['instagram_bio']){
            $this->layout->scroll[] = $this->getComponentText($userdata['vars']['instagram_bio'],['style' => 'uikit_profileview_bodytext']);
        }

        $this->buttons($id, $userdata);

        return $this->layout;
    }


    public function configureBlocks($vars){
        $num = 1;

        $this->layout->scroll[] = $this->getComponentSpacer(10);

        if(isset($vars['profile_comment'])){
            $col[] = $this->getComponentText($vars['profile_comment'],[],['margin' => '10 0 0 0','font-size' => '14','color' => '#757675']);
            $this->layout->scroll[] = $this->getComponentRow($col,[],['margin' => '5 15 5 15']);
            unset($col);
        }

        while($num < 8){
            $title = $this->model->getConfigParam('settings_fields_'.$num.'_title');

            if($title){
                $col[] = $this->getComponentText($title,[],['font-size' => '20','margin' => '10 0 5 0','text-align' => 'center']);
                $col[] = $this->getComponentDivider();
                $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '0 15 0 15']);
                unset($col);

                $fields = $this->model->getConfigParam('settings_fields_'.$num .'_variables');
                $fields = explode(chr(10), $fields);

                foreach($fields as $field){
                    $config = explode(';', $field);

                    if(isset($config[0]) AND isset($config[1])){
                        $var_title = $config[0];
                        $var = trim((string)$config[1]);

                        if(isset($vars[$var])){

                            if(stristr($vars[$var], '{')){
                                $content = json_decode($vars[$var],true);
                                $content = implode(', ', $content);
                            } else {
                                $content = $vars[$var];
                            }

                            $col[] = $this->getComponentText($var_title,[],['margin' => '10 0 0 0','width' => '35%','font-size' => '14']);
                            $col[] = $this->getComponentText($content,[],['margin' => '0 0 0 0','color' => '#757675','font-size' => '14']);
                            $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '5 15 5 15','vertical-align' => 'top']);
                            unset($col);
                        }
                    }
                }
            }

            $num++;
        }

        $this->layout->scroll[] = $this->getComponentSpacer(20);


    }

    public function getBlockButton($id)
    {


        if(isset($this->userdata['hidden']) AND $this->userdata['hidden']){
            $onclick[] = $this->getOnclickSubmit('Controller/like/'.$id);
            $onclick[] = $this->getOnclickGoHome();
            $txt = '{#like_user#}';
        } else {
            $onclick[] = $this->getOnclickSubmit('Controller/unlike/'.$id);
            $onclick[] = $this->getOnclickGoHome();
            $txt = '{#hide_user#}';
        }


        $btn[] = $this->components->uiKitPopupMenu([
            ['name' => $txt,'action' => $onclick],
            [
                'name' => '{#report_user#}',
                'div_id' => 'report_div',
                'div' => 'uiKitPopupMenuReportDiv',
                'div_parameters' =>
                    ['items' => [
                        '{#sexual_content#}',
                        '{#offensive#}',
                        '{#fake#}',
                        '{#other#}'
                    ],'action' => 'Controller/report/'.$id
            ]],
        ],['icon' => 'block-icon.png']);

        $layout = new \stdClass();
        $layout->top = '15';
        $layout->height = '30';
        $layout->right = '15';
        $layout->width = '30';
        return array($this->getComponentColumn($btn,array('layout' => $layout)));

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
            $this->layout->footer[] = $this->getComponentSpacer(20);


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

    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();

        return $divs;
    }


}