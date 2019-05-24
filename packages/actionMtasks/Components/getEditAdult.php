<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getEditAdult {

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

    public function getEditAdult($parameters=array(),$adult){
        /** @var BootstrapComponent $this */


        $col[] = $this->getComponentSpacer(20);

        $subcol[] = $this->getComponentDivider();

        $subcol[] = $this->getHintedField(
            '{#name#}',
            'adult_name',
            'text',
            array('value' => $adult->name)

        );

        //$subcol[] = $this->getComponentFormFieldText('',array('hint' => '{#name#}','uppercase' => true,'style' => 'mtask_formfield_add','variable' => 'adult_name'));
        //$subcol[] = $this->getComponentText('',array('style' => 'mtask_formfield_divider'));

        $subcol[] = $this->getHintedField(
            '{#nickname#}',
            'adult_nickname',
            'text',
            array('value' => $adult->nickname)
        );

        //$subcol[] = $this->getComponentFormFieldText('',array('hint' => '{#nickname#}','uppercase' => true,'style' => 'mtask_formfield_add','variable' => 'adult_nickname'));
        //$subcol[] = $this->getComponentText('',array('style' => 'mtask_formfield_divider'));

        if($adult->invited_play_id == null){
            $subcol[] = $this->getHintedField(
                '{#email#}',
                'adult_email',
                'text',
                array('input_type' => 'email','value' => $adult->email)
            );

        } else {
            $subcol[] = $this->getHintedField(
                '{#email#}',
                'adult_email',
                'noedit',
                array('input_type' => 'email', 'value' => $adult->email)
            );
        }

        //$subcol[] = $this->getComponentFormFieldText('',array('hint' => '{#email#}','uppercase' => true,'input_type' => 'email','style' => 'mtask_formfield_add','variable' => 'adult_email'));
        //$subcol[] = $this->getComponentText('',array('style' => 'mtask_formfield_divider'));

        $subcol[] = $this->getComponentText('{#once_added_email_is_sent#}',array('style' => 'mtask_form_disclaimer'));

        $col[] = $this->getComponentColumn($subcol,array(),array('background-color' => '#ffffff'));
        $col[] = $this->getComponentSpacer(20);

        $switch[] = $this->getComponentDivider();
        $row[] = $this->getComponentText('{#set_as_primary#}',
            array('style' => 'mtask_onoffhint'));
        $row[] = $this->getComponentFormFieldOnoff(
            array('style' => 'mtask_onoff', 'value' => $adult->primary_contact, 'variable' => 'adult_primary')
        );
        $switch[] = $this->getComponentRow($row);

        $switch[] = $this->getComponentDivider();

        $col[] = $this->getComponentColumn($switch,array(),array('background-color' => '#ffffff'));
        return $this->getComponentColumn($col,array());



    }

}
