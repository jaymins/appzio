<?php

namespace packages\actionMleaderboard\Components;

trait uiKitIntroWithButtons {


    public function uiKitIntroWithButtons(array $items, array $params=array()){


        foreach ($items as $item){
            $swipe[] = $this->uiKitIntroWithButtonsGetItem($item);
        }

        if(isset($swipe)){
            $col[] = $this->getComponentSwipe($swipe,array('hide_scrollbar' => 1),array());
            return $this->getComponentColumn($col);
        }

        return $this->getComponentText('no items');

    }


    public function uiKitIntroWithButtonsGetItem($item){

        $ratio = $this->screen_width / $this->screen_height;

        if(isset($item['icon'])){
            if($ratio < 0.57) {
                $icon[] = $this->getComponentImage($item['icon'], array(), array(
                    'margin' => '0 0 0 0',
                    'height' => $this->screen_width
                ));
                $col[] = $this->getComponentColumn($icon, array(), array('text-align' => 'center', 'margin' => '40 0 20 0'));
            } else {
                $icon[] = $this->getComponentImage($item['icon'], array(), array(
                    'margin' => '0 0 0 0',
                    'height' => $this->screen_width
                ));
                $col[] = $this->getComponentColumn($icon, array(), array('text-align' => 'center', 'margin' => '40 0 20 0'));
            }
        }

        if(isset($item['title'])){
            if($ratio < 0.57){
                $col[] = $this->getComponentText($item['title'],array('style' => 'uikit_intro_title'));
            } else {
                $col[] = $this->getComponentText($item['title'],array('style' => 'uikit_intro_title_small'));
            }
        }

        if(isset($item['description'])){
            $col[] = $this->getComponentText($item['description'],array('style' => 'uikit_intro_description_tat'), array(
                'font-size' => '16',
                'text-align' => 'center',
                'margin' => '0 40 10 40',
                'color' => '#fbe327'
            ));
        }

        if(isset($item['buttons'])){

            if(count($item['buttons']) == 1 AND isset($item['buttons'][0]['title']) AND isset($item['buttons'][0]['onclick'])){
                $btns[] = $this->getComponentText($item['buttons'][0]['title'],array(
                        'onclick' => $item['buttons'][0]['onclick'],
                        'noanimate' => true)
                    ,array(
                        'margin' => '0 80 0 80',
                        'text-align' => 'center',
                        'border-radius' => '19',
                        'font-size' => '16',
                        'height' => '40',
                        'color' => '#000000',
                        'width' => '100%',
                        'background-color' => '#fbe327'
                    ));
            } elseif(count($item['buttons']) == 2){
                $btns[] = $this->getComponentText($item['buttons'][0]['title'],array(
                        'onclick' => $item['buttons'][0]['onclick'],
                        'noanimate' => true)
                    ,array(
                        'margin' => '0 80 0 80',
                        'text-align' => 'center',
                        'border-radius' => '19',
                        'font-size' => '16',
                        'height' => '40',
                        'color' => '#ffffff',
                        'width' => '100%',
                        'background-color' => $this->colors['button_color']
                    ));

                $btns[] = $this->getComponentText($item['buttons'][1]['title'],array(
                        'onclick' => $item['buttons'][1]['onclick'],
                        'noanimate' => true)
                    ,array(
                        'margin' => '15 80 0 80',
                        'text-align' => 'center',
                        'border-radius' => '19',
                        'font-size' => '16',
                        'height' => '40',
                        'color' => '#000000',
                        'width' => '100%',
                        'border-color' => '#000000',
                        'border-width' => '1'
                    ));

                /*                $btns[] = $this->uiKitDoubleButtons($item['buttons'][0]['title'],$item['buttons'][1]['title'],
                                    array('onclick' => $item['buttons'][0]['onclick'],'back_button' => 1),
                                    array('onclick' => $item['buttons'][1]['onclick'],'back_button' => 1),
                                    array(),array(),'#F0F3F8');*/
            }


            if(isset($btns)){
                $col[] = $this->getComponentColumn($btns,array(),array(
                    'floating' => '1',
                    'vertical-align' => 'bottom',
                    'width' => $this->screen_width,
                    'text-align' => 'center',
                    'margin' => '30 0 30 0'));
            }

        }

        if(isset($col)){
            return $this->getComponentColumn($col,array(),array('height' => $this->screen_height - 50,'margin' => '0 0 0 0'));
        } else {
            return $this->getComponentText('no info');
        }



    }





}