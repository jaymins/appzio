<?php

namespace BootstrapThemes\Uikit2\Components;

trait themeButton
{

    public function themeButton($title,$onclick,$icon='fb-icon-f.png',$color='grey',$variable=false)
    {
        $col[] = $this->getComponentText($title,['style' => 'swiss8_button_text','uppercase' => true]);

        $params['style'] = 'swiss8_button_icon';

        if($variable){
            $params['variable'] = $variable;
        }

        $col[] = $this->getComponentImage($icon,$params);

        return $this->getComponentRow($col,[
            'style'=>'swiss8_button_'.$color,
            'onclick'=>$onclick],[]);





    }

}