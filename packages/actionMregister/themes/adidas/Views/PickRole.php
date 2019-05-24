<?php

namespace packages\actionMregister\themes\adidas\Views;
use packages\actionMregister\Views\Pagetwo as BootstrapView;
use packages\actionMregister\themes\adidas\Components\Components;

class PickRole extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;
    public $tab;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->model->rewriteActionField('background_image_portrait', '');

//        $this->model->rewriteActionConfigField('background_color', '#000000');



        $this->model->rewriteActionConfigField('hide_menubar', '1');

        if ($this->getData('mode', 'string') == 'close') {
            $this->layout->scroll[] = $this->getComponentText('{#creating_your_account#}', array('style' => 'mreg_general_text'));
            $this->layout->onload[] = $this->getOnclickCompleteAction();
            return $this->layout;
        }

        $image = $this->model->getConfigParam('actionimage1');
        $this->layout->scroll[] = $this->components->HeaderWithImageBgr($image, array(
            'title' => '{#Connect_with_fans#}',
            'icon' => 'adidas_logo.png'
        ));

        $this->layout->scroll[] = $this->uiKitDefaultHeader('{#select_your_role#}', [], [
            'text-align' => 'center',
            'font-size' => '16',
            'padding' => '10 0 10 0',
        ]);

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentColumn(array(
                $this->getComponentText('{#fan#}',
                    array('style' => 'pickrole_wide_button_text')
                ),
                $this->getComponentText('{#i_am_a_fan_info#}',
                    array('style' => 'pickrole_wide_button_info')
                )
                ),
                array( 'onclick' => $this->getOnclickSubmit('set_role_fan')),
                array(
                    'width' => '48%',
                    'height' => '120',
                    'background-color' => '#c0c1c4',
                    'margin' => '5 5 5 5',
                    'vertical-align' => 'middle',
                )
            ),
            $this->getComponentColumn(array(
                    $this->getComponentText('{#fan_club#}',
                        array('style' => 'pickrole_wide_button_text')
                    ),
                    $this->getComponentText('{#i_am_fan_club_representative_info#}',
                        array('style' => 'pickrole_wide_button_info')
                    )
                ),
                array(
//                    'onclick' => $this->getOnclickSubmit('set_role_club')
                ),
                array(
                    'width' => '48%',
                    'height' => '120',
                    'background-color' => '#c0c1c4',
                    'margin' => '5 5 5 5',
                    'vertical-align' => 'middle',
                )
            )
        ));

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentColumn(array(
                $this->getComponentText('{#venue#}',
                    array('style' => 'pickrole_wide_button_text')
                ),
                $this->getComponentText('{#i_own_a_venue#}',
                    array('style' => 'pickrole_wide_button_info')
                )
                ),
                array('onclick' => $this->getOnclickSubmit('set_role_venue')),
                array(
                    'width' => '48%',
                    'height' => '120',
                    'background-color' => '#c0c1c4',
                    'margin' => '5 5 5 5',
                    'vertical-align' => 'middle',
                )
            )), array(), array('text-align' => 'center')
        );

        $this->layout->footer[] = $this->getComponentSpacer('30');

        return $this->layout;
    }

}