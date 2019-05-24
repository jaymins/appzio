<?php

/*

    Layout code codes here. It should have structure of
    $this->data->scroll[] = $this->getElement();

    supported sections are header,footer,scroll,onload & control
    and they should always be arrays

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');

class PostitemMobileclassifiedsView extends PostitemMobileclassifiedsController {

    public $data;
    public $theme;
    public $picWidth;
    public $picHeight;
    public $margin;
    public $deleting;

    public $itemId;

    public function tab1(){
        $this->data = new StdClass();

        if($this->getSavedVariable('logged_in') != 1){
            $this->loginPrompt();
            return $this->data;
        }

        $this->setDimensions();

        if (is_numeric($this->menuid) || $this->menuid == 'post-item') {
            $this->itemId = $this->menuid;
            $this->sessionSet('current_item', $this->menuid);
        }

        $this->itemsModel->id = $this->itemId;
        $this->itemsModel->bumpUpItemPics();


        if ($this->menuid == 'form-submitter') {
            if ($this->validate()) {
                $this->setItemPictures();
                $this->itemsModel->handleSubmit();
                $this->clearSavedPictureVariables();
                $this->data->onload = $this->getRedirect( $this->getActionidByPermaname( 'items' ) );
            }
        } elseif ($this->menuid == 'edit-item') {
            if ($this->validate()) {
                $this->setItemPictures();
                $this->itemsModel->handleSubmit('update', $this->sessionGet('current_item'));
                $this->clearSavedPictureVariables();
                $this->data->onload = $this->getRedirect( $this->getActionidByPermaname( 'items' ) );
            }
        } elseif ($this->menuid == 'delete-item') {
            $this->itemsModel->deleteItem($this->sessionGet('current_item'));
            $this->data->onload = $this->getRedirect( $this->getActionidByPermaname( 'items' ) );
        } elseif ($this->menuid == 'delete-image') {
            $this->deleting = true;
        } elseif(strstr($this->menuid,'imgdel-')){
            $del = str_replace('imgdel-','',$this->menuid);
            $imageIndex = str_replace('pic','',$del);
            if (is_numeric($this->sessionGet('current_item'))) {
                $this->itemsModel->deletePicture($this->sessionGet('current_item'), $imageIndex);
            }
            $this->deleteVariable($del);
            $this->bumpUpItemPics();
            $this->loadVariableContent(true);
            $this->deleting = false;
        }

        if ($this->sessionGet('current_item') == 'post-item') {
            $this->postItem();
        } else {
            $item = $this->itemsModel->getItem($this->sessionGet('current_item'));
            $this->editItem($item);
        }

        return $this->data;
    }

    public function loginPrompt(){
        $onclick = $this->getOnclick('permaname',true,'login');

        if($this->getConfigParam('actionimage1')){
            $this->data->scroll[] = $this->getImage($this->getConfigParam('actionimage1'),array('imgwidth' => '900'));
        }

        $this->data->scroll[] = $this->getText('{#please_login_first#}',array('style' => 'login_prompt'));
        $this->data->scroll[] = $this->getText('{#you_need_to_be_logged_in_before_you_can_post_items#}',array('style' => 'login_text'));
        $this->data->footer[] = $this->getText('{#login#}', [
            'background-color' => $this->color_topbar,
            'color' => '#FFFFFF',
            'padding' => '20 0 20 0',
            'text-align' => 'center',
            'onclick' => $onclick]);

    }

    private function setDimensions()
    {
        $this->margin = 8;
        $this->picWidth = ($this->screen_width - ($this->margin * 5) ) / 4;
        $this->picHeight = $this->picWidth;
    }

    public function postItem() {

        return $this->getForm();
    }

    public function editItem($item) {

        return $this->getForm($item);
    }

    public function getForm($item = null)
    {
        $itemPictures = !empty($item) ? count(array_filter(json_decode($item['pictures']))) : 0;
        $picturesLeft = $this->picturesLeft($itemPictures);
        $this->data->scroll[] = $this->getRow([
            $this->getText('Add Photos', [
                'color' => '#9A9A9A'
            ]),
            $this->getText($picturesLeft . ' left', [
                'color' => 'C9C9C9',
                'floating' => '1',
                'float' => 'right'
            ])
        ], [
            'padding' => $this->margin . ' ' . $this->margin . ' 0 ' . $this->margin,
            'background-color' => '#FFFFFF'
        ]);

        $this->data->scroll[] = $this->getGrid($item);

        $categoriesClicker = new StdClass();
        $categoriesClicker->action = 'open-action';
        $categoriesClicker->action_config = $this->getActionidByPermaname('categorieslisting');
        $categoriesClicker->id = 'open-categories-popup';
        $categoriesClicker->open_popup = '1';
        $categoriesClicker->sync_open = '1';
        $categoriesClicker->back_button = '1';

        $category_heading = !is_null($item) ? $item['category'] : 'Choose Category';

        if ($this->getSubmitVariable('category')) {
            $category_heading = $this->getSubmitVariable('category');
        }

        $this->data->scroll[] = $this->getRow([
            $this->getText($category_heading, [
                'variable' => 'category',
                'padding' => '15 10 15 10',
                'color' => '#4B4B4B'
            ]),
            $this->getFieldtext($category_heading, array(
                'variable' => $this->getVariableId( 'category' ),
                'width' => 1,
                'height' => 1,
                'opacity' => 0,
            )),
            $this->getText('>', [
                'padding' => '12 10 12 0',
                'color' => 'D4D4D4',
                'floating' => '1',
                'float' => 'right'
            ])
        ], [
            'margin' => '12 ' . $this->margin . ' 0 ' . $this->margin,
            'background-color' => '#FFFFFF',
            'border-radius' => 3,
            'shadow-color' => '#DFE3E6',
            'shadow-offset' => '0 3',
            'shadow-opacity'  => '0.9',
            'onclick' => $categoriesClicker
        ]);

        $title_heading = !is_null($item) ? $item['title'] : '';

        if ($this->getSubmitVariable('title')) {
            $title_heading = $this->getSubmitVariable('title');
        }

        $this->data->scroll[] = $this->getRow([
            $this->getFieldtext($title_heading, [
                'hint' => '{#title#}',
                'variable' => 'title',
                'padding' => '15 10 15 10',
                'color' => '#9F9F9F',
                'width' => '100%'
            ])
        ], [
            'margin' => '12 ' . $this->margin . ' 0 ' . $this->margin,
            'background-color' => '#FDFDFD',
            'border-radius' => 3,
            'shadow-color' => '#DFE3E6',
            'shadow-offset' => '0 3',
            'shadow-opacity'  => '0.9'
        ]);

        $description_heading = !is_null($item) ? $item['description'] : '';

        if ($this->getSubmitVariable('description')) {
            $description_heading = $this->getSubmitVariable('description');
        }

//        $this->data->scroll[] = $this->getFieldtextarea($description_heading, [
//            'hint' => '{#description#}',
//            'variable' => 'description',
//            'padding' => '15 10 15 10',
//            'color' => '9F9F9F',
//            'margin' => $this->margin . ' ' . $this->margin . ' 0 ' . $this->margin,
//            'background-color' => '#FDFDFD',
//            'border-radius' => 3,
//            'shadow-color' => '#DFE3E6',
//            'shadow-offset' => '0 3',
//            'shadow-opacity'  => '0.9'
//        ]);

        $this->data->scroll[] = $this->getRow([
            $this->getFieldtextarea($description_heading, [
                'hint' => '{#description#}',
                'variable' => 'description',
                'padding' => '15 10 15 10',
                'color' => '#9F9F9F',
                'width' => '100%',
//                'border-radius' => '3',
            ])
        ], [
            'margin' => '12 ' . $this->margin . ' 0 ' . $this->margin,
            'background-color' => '#FDFDFD',
            'border-radius' => '3',
            'shadow-color' => '#DFE3E6',
            'shadow-offset' => '0 3',
            'shadow-opacity'  => '0.9'
        ]);

        $price_heading = !is_null($item) ? $item['price'] / 100 : '';

        if ($this->getSubmitVariable('price')) {
            $price_heading = $this->getSubmitVariable('price');
        }

        $this->data->scroll[] = $this->getRow([
            $this->getFieldtext($price_heading, [
                'hint' => '{#price#}',
                'variable' => 'price',
                'input_type' => 'number',
                'padding' => '15 10 15 10',
                'color' => '#9F9F9F',
                'width' => '100%'
            ]),
            $this->getImage('price_dollar_sign.png', [
                'width' => '22',
                'padding' => '10 10 10 0',
                'color' => 'D4D4D4',
                'floating' => '1',
                'float' => 'right'
            ])
        ], [
            'margin' => '12 ' . $this->margin . ' 12 ' . $this->margin,
            'background-color' => '#FDFDFD',
            'border-radius' => 3,
            'shadow-color' => '#DFE3E6',
            'shadow-offset' => '0 3',
            'shadow-opacity'  => '0.9'
        ]);

        $this->data->footer[] = $this->formButtons($item);
    }

    public function getGrid($item = null)
    {
        $rows = [];
        $column = [];
        $pictures = json_decode($item['pictures']);

        for ($i = 0; $i < 8; $i++) {
            if ($i % 4 == 0) {
                $rows[] = $this->getRow($column, [
                    'padding' => '0 ' . $this->margin . ' 0 ' . $this->margin,
                    'background-color' => '#FFFFFF'
                ]);
                $rows[] = $this->getSpacer($this->margin, [
                    'background-color' => '#FFFFFF'
                ]);

                unset($column);
            }

            $picture = !empty($this->getSavedVariable('pic' . ($i + 1))) ? $this->getSavedVariable('pic' . ($i + 1)) : '';
            $column[] = $this->getItemImage('pic' . ($i + 1), $picture);
            $this->saveVariable( 'pic' . ($i + 1), $picture);
            $column[] = $this->getVerticalSpacer($this->margin);
        }

        $rows[] = $this->getRow($column, [
            'padding' => '0 ' . $this->margin . ' ' . $this->margin . ' ' . $this->margin,
            'background-color' => '#FFFFFF'
        ]);

        $rows[] = $this->getRow([
            $this->getText('Del', [
                'floating' => 1,
                'float' => 'right',
                'onclick' => $this->getOnclickEvent( $this->deleting ? 'cancel-del' : 'delete-image')
            ])
        ], [
            'padding' => '0 ' . $this->margin * 2 . ' ' . $this->margin . ' ' . $this->margin,
            'background-color' => '#FFFFFF',
            'shadow-color' => '#DFE3E6',
            'shadow-offset' => '0 3',
            'shadow-opacity'  => '0.9'
        ]);

        return $rows;
    }

    public function getItemImage($name, $picture = null)
    {

        $params['width'] = $this->picWidth;
        $params['height'] = $this->picHeight;
        $params['imgcrop'] = 'yes';
        $params['crop'] = 'yes';
        $params['imgwidth'] = '640';
        $params['imgheight'] = '640';
        $params['defaultimage'] = 'profile-add-photo-grey.png';

        if($this->deleting){
            $params['opacity'] = '0.6';
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'submit-form-content';
            $params['onclick']->id = 'imgdel-'.$name;
        } else {
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'upload-image';
            $params['onclick']->max_dimensions = '60';
            $params['onclick']->variable = $this->getVariableId($name);
            $params['onclick']->action_config = $this->getVariableId($name);
            $params['onclick']->sync_upload = true;
        }

        $params['variable'] = $this->getVariableId($name);
        $params['config'] = $this->getVariableId($name);
        $params['debug'] = 1;
        $params['fallback_image'] = 'selecting-image.png';
        $params['priority'] = 9;

        return $this->getImage($picture, $params);
    }

    private function formButtons($item)
    {
        if (!$item) {
            $row = $this->getText('{#Post_Ad#}', [
                'background-color' => $this->color_topbar,
                'color' => '#FFFFFF',
                'padding' => '20 0 20 0',
                'text-align' => 'center',
                'onclick' => $this->getOnclickEvent('form-submitter')

            ]);
        } else {
            $row = $this->getRow([
                $this->getText('{#Save#}', [
                    'background-color' => '#45D194',
                    'width' => $this->screen_width / 2 - 1,
                    'color' => '#FFFFFF',
                    'padding' => '20 0 20 0',
                    'text-align' => 'center',
                    'onclick' => $this->getOnclickEvent('edit-item')

                ]),
                $this->getVerticalSpacer('2', [
                    'color' => '#F0F3F8'
                ]),
                $this->getText('{#Delete#}', [
                    'background-color' => '#E46465',
                    'width' => $this->screen_width / 2 - 1,
                    'color' => '#FFFFFF',
                    'padding' => '20 0 20 0',
                    'text-align' => 'center',
                    'onclick' => $this->getOnclickEvent('delete-item')

                ])
            ]);
        }

        return $row;
    }

    private function getOnclickEvent($param)
    {
        $openItemsClicker = new StdClass();
        $openItemsClicker->action = 'open-action';
        $openItemsClicker->action_config = $this->getActionidByPermaname('items');
        $openItemsClicker->id = 'items';

        $clicker = array();
        $clicker[] = $this->getOnclick('id', false, $param);
//        $clicker[] = $openItemsClicker;

        return $clicker;
    }

    private function picturesLeft($itemPictures)
    {
        $picturesLeft = 8 - $itemPictures;

        for ($i = 1; $i <= 8; $i++) {
            if (!empty($this->getSavedVariable('pic' . $i))) {
                $picturesLeft -= 1;
            }
        }

        return $picturesLeft;
    }

    private function setItemPictures()
    {
        $pictures = [];

        for ($i = 1; $i <= 8; $i++) {
            $pictures[] = $this->getSavedVariable('pic' . $i);
        }

        $this->itemsModel->setPictures($pictures);
    }

    public function bumpUpItemPics()
    {
        /* current state of affairs */
        $pictures = [];

        /* future desired state */
        $indexes = [];

        for ($i = 1; $i <= 8; $i++) {

            $pic = $this->getSavedVariable('pic' . $i);

            if($pic){
                $current_state[$i] = $pic;
            } else {

            }

            $pictures[$i] = $this->getSavedVariable('pic' . $i);
            $i++;
        }

        print_r($pictures);die();


        echo('here');die();

      /*  for ($i = 1; $i <= 8; $i++) {

            $pic = $this->getSavedVariable('pic' . $i);

            if($pic){
                $current_state[$i] = $pic;
            } else {

            }

            $pictures[$i] = $this->getSavedVariable('pic' . $i);
            $i++;
        }

        foreach ($pictures as $key=>$val){
            if(!$val){

            } else {
                $newarray[] = $val;
            }
        }

        if($pictures != $newarray){

        }*/



        for ($i = 1; $i <= 8; $i++) {
            if (!empty($indexes)) {
                $pictures[array_shift($indexes)] = $this->getSavedVariable('pic' . $i);
                $pictures[] = '';
            } else {
                $pictures[] = $this->getSavedVariable('pic' . $i);
            }

            if (!$pictures[$i - 1]) {
                $indexes[] = $i - 1;
            }
        }

        $this->itemsModel->setPictures($pictures);
        $this->itemsModel->handleSubmit();
    }
}