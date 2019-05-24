<?php

namespace packages\actionMarticles\themes\layouts\Views;

class Checkboxes extends Mainview {

    public $layout;
    public $title;

    public $tab;

	public function tab1() {
		$this->layout = new \stdClass();

		$this->layout->scroll[] = $this->getComponentText('Checkboxes', [], [
		    'padding' => '10 15 10 15',
		    'font-size' => '19',
        ]);

        $this->layout->scroll[] = $this->getComponentDivider([
            'background' => '#dddddd',
        ]);

        $this->getButtons([
            'checkbox 1',
            'checkbox 2',
        ], 'checkbox');

        $this->layout->scroll[] = $this->getComponentSpacer(30);

        $this->layout->scroll[] = $this->getComponentText('Radio Buttons', [], [
            'padding' => '10 15 10 15',
            'font-size' => '19',
        ]);

        $this->layout->scroll[] = $this->getComponentDivider([
            'background' => '#dddddd',
        ]);

        $this->getButtons([
            'radio button 1',
            'radio button 2',
        ], 'radio');

        $this->layout->overlay[] = $this->components->uiKitFloatingButtons([
            [
                'icon' => 'layouts-exit-icon.png',
                'onclick' => $this->getOnclickRoute('Controller/Default', false)
            ],
        ], true);

		return $this->layout;
	}

	private function getButtons( array $items, $type ) {

        $nameList = 'testvar';
        $prefix = 'var';

        foreach ($items as $item) {
            $checkbox = [];

            $variable = $nameList . '-' . trim($item, '{#}');

            $value = 1;
            $active = 0;

            if ( $this->model->getSubmittedVariableByName($prefix . $variable) ) {
                $active = 1;
            }

            if ( $type == 'radio' ) {
                $variable = $nameList;
                $value = trim($item, '{#}');

                if ( $value == $this->model->getSubmittedVariableByName($prefix . $variable) ) {
                    $active = 1;
                }
            }

            $icon = $type == 'radio' ? 'layouts-selected-icon-radio.png' : 'layouts-selected-icon.png';

            $selectstate = array(
                'variable' => $prefix . $variable,
                'style_content' => array(
                    'background-image' => $this->getImageFileName($icon),
                    'background-size' => 'contain',
                    'width' => '30',
                    'height' => '30',
                    'text-align' => 'center',
                    'vertical-align' => 'middle',
                ),
                'allow_unselect' => 1,
                'variable_value' => $value,
                'animation' => 'fade',
                'active' => $active,
            );

            $checkbox[] = $this->getComponentText(' ', array(
                'variable' => $prefix . $variable,
                'allow_unselect' => 1,
                'variable_value' => 0,
                'selected_state' => $selectstate
            ), array(
                'border-color' => '#000000',
                'border-radius' => '15',
                'width' => '30',
                'height' => '30',
                'vertical-align' => 'middle',
            ));

            $this->layout->scroll[] = $this->getComponentRow([
                $this->getComponentText($item, [], [
                    'text-align' => 'left',
                    'font-size' => '18',
                    'color' => '#004a55',
                    'width' => '80%',
                    'vartical-align' => 'middle',
                ]),
                $this->getComponentColumn($checkbox, array(), array(
                    'color' => '#161616',
                    'vertical-align' => 'middle',
                    'text-align' => 'right',
                    'padding' => '10 0 10 0',
                    'width' => 'auto',
                ))
            ], [], [
                'margin' => '5 15 5 15'
            ]);

            $this->layout->scroll[] = $this->getComponentDivider([
                'background' => '#dddddd'
            ]);
        }

    }

}