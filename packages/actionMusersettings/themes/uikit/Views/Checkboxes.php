<?php

namespace packages\actionMusersettings\themes\uikit\Views;
use packages\actionMusersettings\Views\View as BootstrapView;
use packages\actionMusersettings\themes\uikit\Components\Components;
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

        $this->model->rewriteActionConfigField('background_color', '#161616');
        $this->model->rewriteActionField('background_image_portrait', '');

        $prefix = $this->getData('prefix', 'string');
        $nameList = $this->getData('name_list', 'string');
        $itemList = $this->getData('list_items', 'array');
        $type = $this->getData('type', 'string');

        if ($this->model->getValidationError($nameList)) {
            $error[] = $this->getComponentText($this->model->getValidationError($nameList), array('style' => 'steps_error'));
            $this->layout->scroll[] = $this->getComponentRow($error, array(), array('width' => '100%'));
        }

        foreach ($itemList as $item) {
            $row = [];
            $checkbox = [];
            $row[] = $this->getComponentText($item, array('style' => 'jam_pref_checkbox_text'));

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
                	"background-image" => $this->getImageFileName("check_on_new1.png"),
					"background-size" => "contain",
					"width" => "100%",
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
                "background-image" => $this->getImageFileName("check_off.png"),
                "background-size" => "contain",
                "width" => "100%",
                "text-align" => "center",
                "vertical-align" => "middle"
            ));
            $row[] = $this->getComponentRow($checkbox, array(), array(
                'color' => '#161616', 'text-align' => 'center', 'width' => '30',
                'vertical-align' => 'middle', 'padding' => '20 0 20 0'
            ));

            $this->layout->scroll[] = $this->getComponentRow($row);

            $this->layout->scroll[] = $this->getComponentText('', array('style' => 'jam_checkbox_divider'));

        }


        $text = strtoupper($this->model->localize('{#submit#}'));
        $menuId = 'save-' . $nameList;
        if ($prefix) {
            $menuId = 'save-' . $nameList . '|' . $prefix;
        }
        $onclick[] = $this->getOnclickSubmit($menuId);
        $onclick[] = $this->getOnclickClosePopup();

        $buttons[] = $this->components->getComponentText(
            $text,
            array('style' => 'steps_btn_bot_yes', 'onclick' => $onclick));


        $text = strtoupper($this->model->localize('{#cancel#}'));
        $onclickClose = $this->getOnclickClosePopup();
        $buttons[] = $this->components->getComponentText(
            $text,
            array('style' => 'steps_btn_bot_no', 'onclick' => $onclickClose));

        $this->layout->footer[] = $this->getComponentRow($buttons);
        return $this->layout;

    }


}