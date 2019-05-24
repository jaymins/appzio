<?php

namespace packages\actionMshopping\Components;

use Bootstrap\Components\BootstrapComponent;

trait HintedRangeSlider
{
    public function getHintedRangeSlider(string $variable, $params = array())
    {
        /** @var BootstrapComponent $this */
        $minValue = isset($params['min_value']) ? $params['min_value'] : 0;
        $maxValue = isset($params['max_value']) ? $params['max_value'] : 1000;
        $value = isset($params['value']) ? $params['value'] : 500;

        return $this->getComponentColumn(array(
            $this->getSliderLabels($minValue, $maxValue, $value, $variable),
            $this->getRangedSlider($minValue, $maxValue, $value, $variable)
        ), array(
            'style' => 'hinted_range_slider'
        ));
    }

    protected function getSliderLabels($minValue, $maxValue, $value, $variable)
    {
        /** @var BootstrapComponent $this */
        return $this->getComponentRow(array(
            $this->getLeftSliderLabel($minValue),
            $this->getMiddleSliderLabel($value, $variable),
            $this->getRightSliderLabel($maxValue)
        ), array(
            'style' => 'hinted_range_slider_label'
        ));
    }

    protected function getLeftSliderLabel($label)
    {
        return $this->getComponentText($label . ' m', array(
            'style' => 'hinted_range_slider_left_label'
        ));
    }

    protected function getMiddleSliderLabel($label, $variable)
    {
        return $this->getComponentRow(array(
            $this->getComponentText($label, array(
                'variable' => $variable,
                'style' => 'hinted_range_slider_middle_label'
            )),
            $this->getComponentText(' m', array(
                'style' => 'hinted_range_slider_middle_label'
            )),
        ), array(
            'style' => 'hinted_range_slider_middle_wrapper'
        ));
    }

    protected function getRightSliderLabel($label)
    {
        return $this->getComponentText($label . ' m', array(
            'style' => 'hinted_range_slider_right_label'
        ));
    }

    protected function getRangedSlider($minValue, $maxValue, $value, $variable)
    {
        /** @var BootstrapComponent $this */
        return $this->getComponentRangeSlider(array_merge(
            array(
                'min_value' => $minValue,
                'max_value' => $maxValue,
                'step' => 1,
                'variable' => $variable,
                'value' => $value,
            ),
            $this->getSliderStyles()
        ));
    }

    private function getSliderStyles()
    {
        $slider_ball = $this->getImageFileName('slider-ball.png');

        return array(
            'step' => '1',
            'left_track_color' => '#ffc204',
            'right_track_color' => '#bebebe',
            'thumb_color' => '#7ed321',
            'track_height' => '4px',
            'thumb_image' => $slider_ball,
        );
    }
}