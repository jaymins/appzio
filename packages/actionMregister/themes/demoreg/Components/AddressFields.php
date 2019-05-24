<?php

namespace packages\actionMregister\themes\demoreg\Components;
use Bootstrap\Components\BootstrapComponent;

trait AddressFields {

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

    public function getAddressFields($shadowbox=true) {
        /** @var BootstrapComponent $this */

        $content[] = $this->getIconField('street','{#street_address#}',
            'reg_icon_street.png',
            array('value' => $this->model->getSavedVariable('street')
            ));

        $content[] = $this->getAddressDivider();

        $content[] = $this->getIconField('street_address2','{#additional_address_info#}',
            'reg_icon_street_2.png');
        $content[] = $this->getAddressDivider();

        $content[] = $this->getIconField('city','{#city#} *',
            'reg_icon_city.png',
            array('value' => $this->model->getSavedVariable('city'))
        );
        $content[] = $this->getAddressDivider();

        $content[] = $this->getIconField('zip','{#zip#}',
            'reg_icon_zip.png',
            array('value' => $this->model->getSavedVariable('zip'))
        );
        $content[] = $this->getAddressDivider();

        $content[] = $this->getIconField('county','{#county#}',
            'reg_icon_county.png',
            array('value' => $this->model->getSavedVariable('county'))
        );
        $content[] = $this->getAddressDivider();

        $content[] = $this->getIconField('country','{#country#} *',
            'reg_icon_country.png',
            array('value' => $this->model->getSavedVariable('country'))
        );
        $content[] = $this->getAddressDivider();
        if($shadowbox){
            $output[] = $this->getShadowBox($this->getComponentColumn($content,array(),array(
                'width' => '100%'
            )));
        } else {
            $output[] = $this->getComponentColumn($content);
        }

        $textarea = $this->getComponentFormFieldTextArea('',array(
            'variable' => $this->model->getVariableId('profile_comment'),
            'hint' => '{#tell_little_about_yourself#}*',
        ));

        if($shadowbox) {
            $output[] = $this->getShadowBox($textarea);
        } else {
            $output[] = $textarea;
        }

        return $this->getComponentColumn($output);
    }

	private function getAddressDivider(){
            return $this->getComponentText('',array('style' => 'mreg_divider'));
    }

}