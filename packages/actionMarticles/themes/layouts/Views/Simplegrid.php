<?php

namespace packages\actionMarticles\themes\layouts\Views;

class Simplegrid extends Mainview {

    public $layout;
    public $title;

    public $tab;

	public function tab1() {
		$this->layout = new \stdClass();

		$this->layout->scroll[] = $this->getComponentText('Simple grid', [], [
		    'padding' => '10 15 10 15',
		    'font-size' => '19',
        ]);

		$this->getSimpleGrid();

        $this->layout->overlay[] = $this->components->uiKitFloatingButtons([
            [
                'icon' => 'layouts-exit-icon.png',
                'onclick' => $this->getOnclickRoute('Controller/Default', false)
            ],
        ], true);

		return $this->layout;
	}

	public function getSimpleGrid() {

	    $elements_per_row = 3;
	    $chunks = array_chunk($this->getElements(4), $elements_per_row);

        foreach ($chunks as $row_items) {
            $items = [];

            foreach ($row_items as $i => $row_item) {
                $items[] = $this->getComponentColumn([
                    $this->getComponentImage($row_item['image'], [], [
                        'margin' => '0 2 0 0'
                    ])
                ], [], [
                    'width' => 100 / $elements_per_row . '%',
                ]);
            }

            $this->layout->scroll[] = $this->getComponentRow($items, [], [
                'width' => 'auto',
                'vertical-align' => 'middle',
                'margin' => '0 15 4 15',
            ]);
	    }

    }

}