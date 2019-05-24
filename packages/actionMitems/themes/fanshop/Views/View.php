<?php

namespace packages\actionMitems\themes\fanshop\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\Components\Components;
use packages\actionMitems\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class View extends BootstrapView
{
    /* @var ArticleModel */
    public $model;

    /* @var Components */
    public $components;
    public $item;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /**
     * Main view entry point
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->model->setBackgroundColor();
        $this->layout = new \stdClass();

        if ($this->getData('close', 'bool')) {
            $this->layout->onload[] = $this->getOnclickGoHome();
            return $this->layout;
        }

        $item = $this->getData('item', 'object');

        if (empty((array)$item)) {
            $this->layout->scroll[] = $this->getComponentText('Whoops, please try again!', array(), array(
                'color' => '#ffffff',
                'text-align' => 'center',
                'padding' => '20 0 20 0',
            ));
            return $this->layout;
        }

        $this->item = $item;

        // Set the view subject to be the name of the item
        $this->model->rewriteActionField('subject', $item->name);

        $images = json_decode($item->images,true);
        $this->setBlockButton();
        $this->layout->scroll[] = $this->uiKitFullWidthImageSwiper($images);
        //$this->layout->scroll[] = $this->itemCategory();
        $this->layout->scroll[] = $this->itemHeader();
        $this->layout->scroll[] = $this->getAuthorRow();
        $this->layout->scroll[] = $this->renderOtherItems($item);
        $this->layout->scroll[] = $this->uiKitTextBlock($this->item->description);

        if(isset($this->item->pretty_tags) AND is_array($this->item->pretty_tags)){
            $this->layout->scroll[] = $this->uiKitTagList($this->item->pretty_tags);
        }

        $this->layout->scroll[] = $this->getButtons();
        if($this->item->play_id == $this->model->playid){
            $btn1['onclick'] = $this->getOnclickShowDiv('confirmdelete',
                array('background' => 'blur','tap_to_close' => true),
                array('left' => '50','right' => '50','bottom' => $this->screen_height/2 - 200));
            $styles['margin'] = '10 80 15 80';
            $styles['background-color'] = '#B41C11';
            $col[] = $this->uiKitButtonFilled('{#delete_item#}',$btn1,$styles);
            $this->layout->scroll[] = $this->getComponentColumn($col,array(),array('background-color' => '#ffffff'));
        }

        return $this->layout;
    }


    public function setBlockButton()
    {

        if($this->item->play_id == $this->model->playid){
            return true;
        } else {
            $isLiked = $this->getData('isLiked', 'bool');
            $btn[] = $this->components->uiKitButtonBlock(array(
                'isLiked' => $isLiked
            ));
        }

        $layout = new \stdClass();
        $layout->top = '15';
        $layout->height = '30';
        $layout->right = '15';
        $layout->width = '30';
        $this->layout->overlay[] = $this->getComponentColumn($btn,array('layout' => $layout),array());

    }

    public function getDivs() {
        $divs['boost'] = $this->uiKitPurchaseDiv( 'boost', 'send-icon-fancy.png', '{#buy_boost#}', '{#boost_your_post_for_ten_days#} {#it_will_be_shown_as_featured_item_on_top_of_the_listings#}', 'product_boost.01', 'product_boost.01', true );

        if(isset($this->item->id)){
            $onclick[] = $this->getOnclickSubmit('Edit/delete/'.$this->item->id);
            $onclick[] = $this->getOnclickOpenAction('myitems');

            $divs['confirmdelete'] = $this->components->getComponentConfirmationDialog(
                $onclick,'confirmdelete','{#are_you_sure_you_want_to_delete_this_ad#}?',
                array('title' => '{#delete#}','title_cancel' => '{#cancel#}'));
        }

        return $divs;
    }


    public function getButtons(){

        if($this->model->getSavedVariable('logged_in') != 1){
            $btn1['onclick'] = $this->getOnclickSubmit('Intro/default/'.$this->item->id);
            $btn2['onclick'] = $this->getOnclickSubmit('Intro/default/'.$this->item->id);
            return $this->uiKitDoubleButtons('{#buy_this_item#}','{#contact_seller#}',$btn1,$btn2);
        }

        if($this->item->play_id == $this->model->playid){

            if($this->item->featured == 1){
                $btn1['onclick'] = $this->getOnclickSubmit('Edit/default/'.$this->item->id);
                $styles['margin'] = '15 80 15 80';
                return $this->uiKitButtonFilled('{#edit_item#}',$btn1,$styles);
            } else {
                $layout = new \stdClass();
                $layout->top = ($this->screen_height / 2) - 175;
                $layout->right = 25;
                $layout->left = 25;

                $btn1['onclick'] = $this->getOnclickShowDiv('boost',array(
                    'tap_to_close'=>1,
                    'transition'=>'fade',
                    'background'=>'blur',
                    'layout' => $layout
                ));

                $btn2['onclick'] = $this->getOnclickSubmit('Edit/default/'.$this->item->id);
                return $this->uiKitDoubleButtons('{#boost_item#}','{#edit#}',$btn1,$btn2,
                    array(),array(),'#ffffff',true);
            }

        } else {

            $this->model->initMobileMatching();
            $chatid = $this->model->getTwoWayChatId($this->item->play_id);

            $btn1['onclick'] = $this->getOnclickSubmit('Contact/buy/'.$this->item->id);

            $btn2['onclick'][] = $this->getOnclickOpenAction('chat',false,array(
                'id' => $chatid,
                'sync_open' => 1,
                'back_button' => 1
                ));

            $btn1 = $btn2;

            return $this->uiKitDoubleButtons('{#buy_this_item#}','{#contact_seller#}',$btn1,$btn2);
        }
    }

    public function itemHeader(){

        if(!$this->item->country){
            $location = '{#unknown_location#}';
        } else {
            $location = $this->item->country;
            if($this->item->city){
                $location = $this->item->city .', '. $location;
            }
        }

        return $this->uiKitTitlePriceLocation($this->item->name, '$'.$this->item->price, $location);
    }

    public function getAuthorRow()
    {
        $profilepic = isset($this->item->owner['profilepic']) ? $this->item->owner['profilepic'] : 'icon_camera-grey.png';
        $firstname = isset($this->item->owner['firstname']) ? $this->item->owner['firstname'] : '{#anonymous#}';
        $lastname = isset($this->item->owner['lastname']) ? $this->item->owner['lastname'] : '';
        $name = isset($this->item->owner['username']) ? $this->item->owner['username'] : $firstname .' ' .$lastname;

        $name1 = $name.' ({#click_to_show_other_ads#})';

        $onclick_collapsed[] = $this->getOnclickShowElement('otheritems');
        $onclick_collapsed[] = $this->getOnclickShowElement('name_expanded',array('transition' => 'none'));
        $onclick_collapsed[] = $this->getOnclickHideElement('name_collapsed',array('transition' => 'none'));

        $onclick_expanded[] = $this->getOnclickHideElement('otheritems');
        $onclick_expanded[] = $this->getOnclickHideElement('name_expanded',array('transition' => 'none'));
        $onclick_expanded[] = $this->getOnclickShowElement('name_collapsed',array('transition' => 'none'));

        $col[] = $this->uiKitHeaderWithImage($profilepic,$name1,array('onclick' => $onclick_collapsed));
        $out[] = $this->getComponentColumn($col,array('id' => 'name_collapsed'));
        unset($col);

        $col[] = $this->uiKitHeaderWithImage($profilepic,$name ."'s {#other_items#}",array(
            'onclick' => $onclick_expanded,'hide_divider' => true),
            array(
            'background-color' => '#F0F3F8'
        ));
        $out[] = $this->getComponentColumn($col,array('id' => 'name_expanded','visibility' => 'hidden'));
        unset($col);

        return $this->getComponentColumn($out);


    }

    public function itemCategory(){
        $like = 'Controller/star/'.$this->item->id;
        $unlike = 'Controller/unstar/'.$this->item->id;
        $isLiked = $this->getData('isLiked', 'bool');
        $column2 = $this->components->uiKitLikeStar($isLiked, $like, $unlike,30);
        $path = $this->model->getCategoryPath($this->item->category_id);
        return $this->uiKitTwoColumnHeader($path, $column2);
    }


    protected function renderCategory($category)
    {
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->components->getItemTag($category->name)
        ), array(), array(
            'margin' => '5 10 15 10'
        ));
    }

    protected function openChat($artistId)
    {
        $matchUserWithArtist = $this->getOnclickRoute('Controller/match/' . $artistId, false);

        $openChat = new \stdClass();
        $openChat->action = 'open-action';
        $openChat->id = $this->getTwoWayChatId($this->model->playid, $artistId);
        $openChat->back_button = true;
        $openChat->sync_open = true;
        $openChat->viewport = 'bottom';
        $openChat->action_config = $this->model->getActionidByPermaname('chat');

        $matchUserWithArtist[] = $openChat;

        return $matchUserWithArtist;
    }

    protected function getTwoWayChatId($playId, $artistId)
    {
        if ($playId < $artistId) {
            $chatid = $playId . '-chat-' . $artistId;
        } else {
            $chatid = $artistId . '-chat-' . $playId;
        }

        return $chatid;
    }


    protected function renderOtherItems($item)
    {
        $other_items = $this->model->getRelatedItems($item->play_id, $item->id,16);

        if (empty($other_items)) {
            $this->uiKitDivider();
        }

        $col[] = $this->uiKitThreeColumnImageSwiper($other_items);
        $col[] = $this->uiKitDivider();
        
        return $this->getComponentColumn($col,array(
            'id' => 'otheritems',
            'visibility' => 'hidden'
        ),array(
            'text-align' => 'center'
        ));
    }

}
