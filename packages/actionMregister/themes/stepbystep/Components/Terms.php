<?php

namespace packages\actionMregister\themes\stepbystep\Components;
use function array_merge;
use Bootstrap\Components\BootstrapComponent;

trait Terms {

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

    public function getTerms($center=false) {
        /** @var BootstrapComponent $this */

        $onclick_terms = $this->getOnclickOpenAction('terms',false,array(
            'open_popup' => 1
        ));

        $onclick_privacy = $this->getOnclickOpenAction('privacypolicy',false,array(
            'open_popup' => 1
        ));

        if($center){
            $style['text-align'] = 'center';
        } else {
            $style = array();
        }

        $row[] = $this->getComponentText('{#by_tapping_sign_up_you_agree_to_the#} ',array('style' => 'steps_terms'));
        $row[] = $this->getComponentText('{#terms_of_service#}',array('style' => 'steps_terms_clickable','onclick' => $onclick_terms));

        $out[] = $this->getComponentRow($row,array(),$style);
        unset($row);

        $row[] = $this->getComponentText('{#and#} ',array('style' => 'steps_terms'));
        $row[] = $this->getComponentText('{#privacy_policy#}',array('style' => 'steps_terms_clickable','onclick' => $onclick_privacy));

        $out[] = $this->getComponentRow($row,array(),$style);

        $style['margin'] = '5 40 5 40';


        return $this->getComponentColumn($out,array(),$style);
	}

}