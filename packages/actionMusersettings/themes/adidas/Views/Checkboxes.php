<?php

namespace packages\actionMusersettings\themes\adidas\Views;
use packages\actionMusersettings\Views\View as BootstrapView;
use packages\actionMusersettings\themes\adidas\Components\Components;
use function strtoupper;

class Checkboxes extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->model->rewriteActionConfigField('background_color', '#ffffff');
        $this->model->rewriteActionField('background_image_portrait', '');

        $prefix = $this->getData('prefix', 'string');
        $nameList = $this->getData('name_list', 'string');
        $itemList = $this->getData('list_items', 'array');
        $type = $this->getData('type', 'string');

        if ($this->model->getValidationError($nameList)) {
            $error[] = $this->getComponentText($this->model->getValidationError($nameList), array('style' => 'steps_error'));
            $this->layout->scroll[] = $this->getComponentRow($error, array(), array('width' => '100%'));
        }
        $this->layout->header[] = $this->getComponentRow(array(
            $this->getComponentText('Select your {#'. $nameList . '#}', array(), array(
                'color' => '#ffffff',
                'padding' => '20 0 20 0'
            ))
        ), array(), array(
            'background-color' => '#29292c',
            'text-align' => 'center',
            'width' =>'100%'
        ));
        foreach ($itemList as $item) {
            $row = [];
            $checkbox = [];
            $styles = [
                "text-align" => "left",
                "font-size" => "18",
                "color" => "#858585",
                "width" => "80%",
                "margin" => "0 0 0 20",
                "vartical-align" => "middle"
            ];
            $row[] = $this->getComponentText($item,
                array(
//                    'style' => 'jam_pref_checkbox_text'
                ), $styles);

            $variable = $nameList . '-' . trim($item, '{#}');
            $value = 1;

            $active = 0;
            if ($this->model->getSubmittedVariableByName($prefix . $variable)) {
                $active = 1;
            }

            if ($type == 'radio') {
                $variable = $nameList;
                $value = trim($item, '{#}');

                if ($value == $this->model->getSubmittedVariableByName($prefix . $variable)) {
                    $active = 1;
                }
            }

            $selectstate = array(
                'variable' => $prefix . $variable,
                'style_content' => array(
                    "background-image" => $this->getImageFileName("success_1.png"),
                    "background-size" => "contain",
                    "width" => "30",
                    "height" => "30",
                    "text-align" => "center",
                    "vertical-align" => "middle",
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
                "border-color" => '#000000',
                "border-radius"=> "15",
                "width" => "30",
                "height" => "30",
                "text-align" => "center",
                "vertical-align" => "middle",
                'margin' => '0 5 0 0'
            ));
            $row[] = $this->getComponentRow($checkbox, array(), array(
                'color' => '#161616', 'text-align' => 'center', 'width' => '40',
                'vertical-align' => 'middle',
                'padding' => '10 0 10 0',
//                'margin' => '0 10 0 0'
            ));

            $this->layout->scroll[] = $this->getComponentRow($row);

            $this->layout->scroll[] = $this->getComponentText('', array('style' => 'jam_checkbox_divider'));
        }

        $menuId = 'save-' . $nameList;
        if ($prefix) {
            $menuId = 'save-' . $nameList . '|' . $prefix;
        }

        $onclick[] = $this->getOnclickSubmit($menuId);
        $onclick[] = $this->getOnclickClosePopup();

        $buttons[] = $this->getComponentRow(array(
            $this->getComponentText('Add {#'. $nameList . '#}', array(), array(
                "color" => "#333333",
                "padding" => "10 0 10 0",
                "text-align" => "center"
            ))
        ), array(
            'onclick' => $onclick
        ), array(
            "text-align" => "center",
            "background-color" => "#fbe121",
            'width' => '100%',
        ));

        $this->layout->footer[] = $this->getComponentRow($buttons);
        return $this->layout;
    }

}