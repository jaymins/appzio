<?php

namespace packages\actionMarticles\themes\layouts\Views;

class Overlay extends Mainview {

    public $layout;
    public $title;

    public $tab;

	public function tab1() {
		$this->layout = new \stdClass();

		$this->layout->scroll[] = $this->getComponentText('Overlay Images', [], [
		    'padding' => '10 15 10 15',
		    'font-size' => '19',
        ]);

		$this->layout->scroll[] = $this->getComponentRow([
		    $this->getComponentColumn([
		        $this->getComponentRow([
		            $this->getComponentImage('profile.jpg', [
                    ], [
                        'crop' => 'round',
		                'width' => '30',
		                'height' => '30',
                        'vertical-align' => 'middle'
                    ]),
                    $this->getComponentText(strtoupper('An image of a see'), [], [
                        'color' => '#ffffff',
                        'font-size' => '19',
                        'padding' => '15 15 15 15',
                        'vertical-align' => 'middle',
                    ])
                ], [], [
                    'width' => 'auto',
                    'margin' => '0 15 0 15',
                    'vertical-align' => 'bottom',
                ])
            ], [], [
                'width' => 'auto',
                'height' => $this->screen_height / 3,
                'background-image' => $this->getImageFileName('shadow-image-wide.png'),
                'background-size' => 'cover',
            ])
        ], [], [
            'width' => 'auto',
            'height' => $this->screen_height / 3,
            'background-image' => $this->getImageFileName('see.jpg'),
            'background-size' => 'cover',
        ]);

        $this->layout->overlay[] = $this->components->uiKitFloatingButtons([
            [
                'icon' => 'layouts-exit-icon.png',
                'onclick' => $this->getOnclickRoute('Controller/Default', false)
            ],
        ], true);

		return $this->layout;
	}

}