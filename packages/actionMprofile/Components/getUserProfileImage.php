<?php

namespace packages\actionMprofile\Components;

use Bootstrap\Components\BootstrapComponent;
use Bootstrap\Models\BootstrapModel;

trait getUserProfileImage
{
    private $margin;
    private $grid;
    private $deleting;

    /**
     * @param $image string
     * @param array $styles 'margin', 'padding', 'orientation', 'background', 'alignment', 'radius', 'opacity',
     * 'orientation', 'height', 'width', 'align', 'crop', 'text-style', 'font-size', 'text-color', 'border-color',
     * 'border-width', 'font-android', 'font-ios', 'background-color', 'background-image', 'background-size',
     * 'color', 'shadow-color', 'shadow-offset', 'shadow-radius', 'vertical-align', 'border-radius', 'text-align',
     * 'lazy', 'floating' (1), 'float' (right | left), 'max-height', 'white-space' (no-wrap), parent_style
     * @param array $parameters selected_state, variable, onclick, style
     * @return array
     */
    public function getUserProfileImage($image = 'profilepic', $styles = array(), $parameters = array())
    {
        /** @var BootstrapComponent $this */
        $this->setImageGridWidths();
        return $this->getImageMarkup($image, true);
    }

    public function getImageMarkup($name, $mainimage = false)
    {
        /** @var BootstrapComponent $this */

        if ($mainimage) {
            $styles['width'] = $this->grid * 2 + $this->margin;
            $styles['height'] = $this->grid * 2 + $this->margin;
            $params['imgwidth'] = '600';
            $params['imgheight'] = '600';
        } else {
            $styles['width'] = $this->grid;
            $styles['height'] = $this->grid;
            $params['imgwidth'] = '600';
            $params['imgheight'] = '600';
        }

        $styles['imgcrop'] = 'yes';
        $styles['crop'] = 'round';
        $params['defaultimage'] = 'profile-add-photo-grey.png';
        $params['image_fallback'] = 'profile-add-photo-grey.png';

        if ($this->deleting AND $this->model->getSavedVariable($name) AND strlen($this->model->getSavedVariable($name)) > 2) {
            $params['opacity'] = '0.6';
            $params['onclick'] = new \stdClass();
            $params['onclick']->action = 'submit-form-content';
            $params['onclick']->id = 'imgdel-' . $name;
        } else {
            $params['onclick'] = new \stdClass();
            $params['onclick']->action = 'upload-image';
            $params['onclick']->max_dimensions = '600';
            $params['onclick']->variable = $this->model->getVariableId($name);
            $params['onclick']->action_config = $this->model->getVariableId($name);
            $params['onclick']->sync_upload = true;
        }

        $styles['border-width'] = '5';
        $styles['border-color'] = '#FFFFFF';
        $styles['border-radius'] = ($this->grid * 2 + $this->margin) / 2;

        $params['variable'] = $this->model->getVariableId($name);
        $params['config'] = $this->model->getVariableId($name);
        $params['debug'] = 1;
        $params['priority'] = 9;

        return $this->getComponentImage($this->model->getSavedVariable($name), $params, $styles);
    }

    public function setImageGridWidths()
    {
        $width = $this->screen_width ? $this->screen_width : 320;
        $this->margin = 20;
        $this->grid = $width - ($this->margin * 4);
        $this->grid = round($this->grid / 3, 0);
    }
}
