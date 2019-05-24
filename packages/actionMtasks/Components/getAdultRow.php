<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getAdultRow {

    /**
     * @param $content string, no support for line feeds
     * @param array $styles 'margin', 'padding', 'orientation', 'background', 'alignment', 'radius', 'opacity',
     * 'orientation', 'height', 'width', 'align', 'crop', 'text-style', 'font-size', 'text-color', 'border-color',
     * 'border-width', 'font-android', 'font-ios', 'background-color', 'background-image', 'background-size',
     * 'color', 'shadow-color', 'shadow-offset', 'shadow-radius', 'vertical-align', 'border-radius', 'text-align',
     * 'lazy', 'floating' (1), 'float' (right | left), 'max-height', 'white-space' (no-wrap), parent_style
     * @param array $parameters selected_state, variable, onclick, style
     * @return \stdClass
     */

    public function getAdultRow($data,$totalcount=false,$number=false,$tab=false,$parameters=array()){
        /** @var BootstrapComponent $this */

        /** @var BootstrapComponent $this */

        $id_inactive = $number .'_hidden_'.$tab;
        $id_active = $number .'_active_'.$tab;

        if (!$this->model->sessionGet('adult_id')
            && !$this->model->sessionGet('invitation_id')
            && $data->primary_contact == 1) {

            if ($data->invited_play_id) {
                $this->model->sessionSet('adult_id', $data->invited_play_id);
            } else {
                $this->model->sessionSet('invitation_id', $data->id);
            }
        }

        if (($this->model->sessionGet('adult_id') == $data->invited_play_id
                && $data->invited_play_id != null)
            OR $this->model->sessionGet('invitation_id') == $data->id){
            $selector[] = $this->getComponentImage('icon-select.png', array('style' => 'mtasks_primary_icon_inactive','id' => $id_inactive,'visibility' => 'hidden'));
            $selector[] = $this->getComponentImage('icon-select.png', array('style' => 'mtasks_primary_icon','id' => $id_active));
        } else {

            $selector[] = $this->getComponentImage('icon-select.png', array('style' => 'mtasks_primary_icon_inactive','id' => $id_inactive));
            $selector[] = $this->getComponentImage('icon-select.png', array('style' => 'mtasks_primary_icon','id' => $id_active,'visibility' => 'hidden'));
        }

//        $adultinfo[] = $this->getComponentText($this->model->sessionGet('adult_id'),array('style' => 'mtasks_al_name'));

        $adultinfo[] = $this->getComponentText($data->name,array('style' => 'mtasks_al_name'));
        $adultinfo[] = $this->getAdultNickName($data);
        $adultinfo[] = $this->getComponentText('{#existing_deals#}: ' .$data->deals,array('style' => 'mtasks_al_deals'));

        $row[] = $this->getComponentColumn($adultinfo,array(),array(
            'margin' => '0 0 0 0',
            'width' => '70%'
        ));

        $row[] = $this->getComponentColumn($selector,array(),array('margin' => '35 20 0 10', "floating"=> "1", "float" => "right"));

//        $row[] = $this->getComponentImage('div-fwd-icon.png',array('style' => 'mtasks_select_icon'));
        $col[] = $this->getComponentRow($row,array(),array('vertical-align' => 'middle','height' => '100'));


        $onclick[] = $this->getOnclickShowElement($id_active);
        $onclick[] = $this->getOnclickHideElement($id_inactive);

        $counter = 0;

        while($counter < $totalcount){
            if($counter != $number){
                $id_inactive = $counter .'_hidden_'.$tab;
                $id_active = $counter .'_active_'.$tab;
                $onclick[] = $this->getOnclickHideElement($id_active,array('transition' => 'none'));
                $onclick[] = $this->getOnclickShowElement($id_inactive,array('transition' => 'none'));
            }

            $counter++;
        }

        if(isset($parameters['close'])){
            $onclick[] = $this->getOnclickClosePopup();
        } elseif($tab != 1) {
            $onclick[] = $this->getOnclickTab(1);
        }

        if(isset($parameters['close'])) {
            if($data->invited_play_id) {
                $onclick[] = $this->getOnclickSubmit('Controller/setadultid/'.$data->invited_play_id,array('loader_off' => true));
            } else {
                $onclick[] = $this->getOnclickSubmit('Controller/setinvitationid/'.$data->id,array('loader_off' => true));
            }
        } else {
            if($data->invited_play_id){
                $onclick[] = $this->getOnclickSubmit('Controller/setadultid/'.$data->invited_play_id,array('loader_off' => true));
            } else {
                $onclick[] = $this->getOnclickSubmit('Controller/setinvitationid/'.$data->id,array('loader_off' => true));
            }
        }

        unset($row);
        $editadult = $this->getOnclickRoute('Controller/editadult/'.$data->id);
        $row[] = $this->getComponentColumn([$this->getAdultImage($data,$editadult)], array(), array('margin' => '0 0 0 10','vertical-align' => 'middle'));
        $row[] = $this->getComponentColumn($col,array('onclick' => $onclick),array('width' => 'auto'));


/*        $row[] = $this->getComponentImage('editing-icon-adult.png',array('onclick' => $editadult),array('height' => '40','floating' => '1', 'float' => 'right',
            'margin' => '0 80 0 0'));*/

        $output[] = $this->getComponentDivider();
        $output[] = $this->getComponentRow($row,array('style' => 'mtasks_adult_button'));
        $output[] = $this->getComponentDivider();

        return $this->getComponentColumn($output,array(),array('margin' => '20 0 0 0'));

    }

    public function getAdultImage($data,$onclick=false){

        if($onclick){
            if(isset($data->profilepic)){
                return $this->getComponentImage($data->profilepic,array('style' => 'mreg_persona_icon','onclick' => $onclick));
            } else {
                return $this->getComponentImage('mreg-persona-icon.png',array('style' => 'mreg_persona_icon','onclick' => $onclick));
            }
        } else {
            if(isset($data->profilepic)){
                return $this->getComponentImage($data->profilepic,array('style' => 'mreg_persona_icon'));
            } else {
                return $this->getComponentImage('mreg-persona-icon.png',array('style' => 'mreg_persona_icon'));
            }
        }

    }

    public function getAdultNickName($data){
        if($data->status == 'invited'){
            return $this->getComponentText('{#invite_sent#}',array('uppercase' => 1, 'style' => 'mtasks_al_invited'));
        } else {
            return $this->getComponentText('@' .$data->nickname,array('style' => 'mtasks_al_nickname'));
        }
    }

}
