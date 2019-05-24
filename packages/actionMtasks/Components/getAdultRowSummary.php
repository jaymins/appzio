<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getAdultRowSummary {

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

    public function getAdultRowSummary($data,$parameters=array()){
        /** @var BootstrapComponent $this */

        /** @var BootstrapComponent $this */

        $row[] = $this->getAdultImage($data,false);

        $adultinfo[] = $this->getComponentText($data->nickname,array('style' => 'mtasks_al_name'));
        $adultinfo[] = $this->getAdultNickName($data);
        $adultinfo[] = $this->getComponentText('{#existing_deals#}: ' .'0',array('style' => 'mtasks_al_deals'));

        $row[] = $this->getComponentColumn($adultinfo,array());
        $col[] = $this->getComponentRow($row,array(),array('vertical-align' => 'middle','margin' => '0 15 0 15'));

        if($data->invited_play_id){
            $onclick = $this->getOnclickRoute('Addtask/default/'.$data->id,true,array('adult_id' => $data->invited_play_id));
        } else {
            $onclick = $this->getOnclickRoute('Addtask/default/'.$data->id,true,array('invitation_id' => $data->id));
        }
        $onclick[] = $this->getOnclickTab(1);

        $adult = $this->model->sessionGet('adult_id');

        $col[] = $this->getComponentSpacer('12');

        return $this->getComponentColumn($col,array(
            //'onclick'=>$onclick,
            'style' => 'mtasks_adult_button'));



    }

}
