<?php

namespace packages\actionMusersettings\themes\uikit\Components;
use Bootstrap\Components\BootstrapComponent;

trait getPhotoField {

    public $margin;
    public $grid;
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

    public function getPhotoField(string $content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        $this->setGridWidths();

        /* row 1 with big picture and two small ones */

        $column[] = $this->getProfileImage('profilepic',true);
        $column[] = $this->getComponentVerticalSpacer($this->margin);

        $row[] = $this->getProfileImage('profilepic2');
        $row[] = $this->getComponentSpacer($this->margin);
        $row[] = $this->getProfileImage('profilepic3');

        $column[] = $this->getComponentColumn($row);

        $block[] = $this->getComponentRow($column,array('margin' => '0 ' .$this->margin .' 0 ' .$this->margin));
        $block[] = $this->getComponentSpacer($this->margin);

        unset($column);
        unset($row);

        $column[] = $this->getProfileImage('profilepic4');
        $column[] = $this->getComponentVerticalSpacer($this->margin);
        $column[] = $this->getProfileImage('profilepic5');
        $column[] = $this->getComponentVerticalSpacer($this->margin);
        $column[] = $this->getProfileImage('profilepic6');

        $block[] = $this->getComponentRow($column);

        return $this->getComponentColumn($block, array(),
            array(
                'margin' => $this->margin .' ' .$this->margin .' ' . $this->margin . ' ' .$this->margin
            ));

	}

    public function setGridWidths(){
        $width = $this->screen_width ? $this->screen_width : 320;
        $this->margin = 20;
        $this->grid = $width - ($this->margin*4);
        $this->grid = round($this->grid / 3,0);
    }

    public function getProfileImage($name, $mainimage = false){

        if($mainimage){
            $style['width'] = $this->grid*2 + $this->margin;
            $style['height'] = $this->grid*2 + $this->margin;
            $params['imgwidth'] = "600";
            $params['imgheight'] = "600";

        } else {
            $style['width'] = $this->grid;
            $style['height'] = $this->grid;
            $params['imgwidth'] = "600";
            $params['imgheight'] = "600";
        }

        $params['imgcrop'] = 'yes';
        $style['background-color'] = '#2d2d2d';

        $params['defaultimage'] = 'icon_camera.png';

//        if($this->deleting AND $this->model->getSavedVariable($name) AND strlen($this->model->getSavedVariable($name)) > 2){
//            $params['opacity'] = '0.6';
//            $params['onclick'] = new StdClass();
//            $params['onclick']->action = 'submit-form-content';
//            $params['onclick']->id = 'imgdel-'.$name;
//        } else {

            $params['onclick'] = $this->getOnclickImageUpload($name,array(
                'max_dimensions' => '600',
                'sync_upload' => 1
            ));
//        }

        $params['variable'] = $this->model->getVariableId($name);

        return $this->getComponentImage($this->model->getSavedVariable($name),$params, $style);
    }

}
