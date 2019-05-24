<?php

namespace packages\actionMarticles\themes\layouts\Views;

class Swipers extends Mainview {

    public $layout;
    public $title;

    public $tab;

	public function tab1() {
		$this->layout = new \stdClass();

		$this->layout->scroll[] = $this->getComponentText('Swiper examples', [], [
		    'padding' => '10 15 10 15',
		    'margin' => '0 0 20 0',
		    'font-size' => '19',
        ]);

		$this->getSingleSelectionSlider();

		$this->getDoubleSelectionSlider();

        $this->layout->overlay[] = $this->components->uiKitFloatingButtons([
            [
                'icon' => 'layouts-exit-icon.png',
                'onclick' => $this->getOnclickRoute('Controller/Default', false)
            ],
        ], true);

		return $this->layout;
	}

    private function getSingleSelectionSlider() {
        $this->layout->scroll[] = $this->components->uiKitBackgroundHeader('Single selection slider');

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentText('0 km', ['style' => 'uikit_slider_left_hint']),
            $this->getComponentRow([
                $this->getComponentText('50', ['variable' => 'single-slider', 'style' => 'uikit_slider_hint']),
                $this->getComponentText(' km', ['style' => 'uikit_slider_hint']),
            ], [
                'style' => 'uikit_slider_indicator'
            ]),
            $this->getComponentText('100 km', ['style' => 'uikit_slider_right_hint']),
        ], [
            'style' => 'uikit_slider_hintrow'
        ]);

        $this->layout->scroll[] = $this->getComponentRangeSlider([
            'min_value' => 0,
            'max_value' => 100,
            'step' => 1,
            'variable' => 'single-slider',
            'left_track_color' => '#ffc204',
            'right_track_color' => '#bebebe',
            'thumb_color' => '#7ed321',
            'track_height' => '2',
            'value' => '50'
        ], [
            'width' => '100%',
            'margin' => '15 15 30 15'
        ]);
    }

    private function getDoubleSelectionSlider() {
        $this->layout->scroll[] = $this->components->uiKitBackgroundHeader('Double selection slider');

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentColumn([
                $this->getComponentRow([
                    $this->getComponentText('Left selection: ', ['style' => 'uikit_slider_hint']),
                    $this->getComponentText('20', ['variable' => 'double-slider-1', 'style' => 'uikit_slider_hint']),
                ])
            ], [], [
                'width' => '50%',
                'text-align' => 'left',
            ]),
            $this->getComponentColumn([
                $this->getComponentRow([
                    $this->getComponentText('Right selection: ', ['style' => 'uikit_slider_hint']),
                    $this->getComponentText('80', ['variable' => 'double-slider-2', 'style' => 'uikit_slider_hint']),
                ])
            ], [], [
                'width' => '50%',
                'text-align' => 'right'
            ]),
        ], [
            'style' => 'uikit_slider_hintrow'
        ]);

        $this->layout->scroll[] = $this->getComponentRangeSlider([
            'min_value' => 0,
            'max_value' => 100,
            'step' => 1,
            'variable' => 'double-slider-1',
            'variable2' => 'double-slider-2',
            'left_track_color' => '#ffc204',
            'right_track_color' => '#bebebe',
            'thumb_color' => '#7ed321',
            'track_height' => '2',
            'value' => '20',
            'value2' => '80'
        ], [
            'width' => '100%',
            'margin' => '15 15 15 15'
        ]);
    }

}