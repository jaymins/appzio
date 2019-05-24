<?php

namespace packages\actionMarticles\themes\layouts\Views;


class Extendedgrid extends Simplegrid {

    public $layout;
    public $title;

    public $tab;

	public function tab1() {
		$this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentText('Extended grid', [], [
            'padding' => '10 15 10 15',
            'font-size' => '19',
        ]);

        $this->getExtendedGrid();

        $this->layout->overlay[] = $this->components->uiKitFloatingButtons([
            [
                'icon' => 'layouts-exit-icon.png',
                'onclick' => $this->getOnclickRoute('Controller/Default', false)
            ],
        ], true);

		return $this->layout;
	}

    public function getExtendedGrid() {

        $elements_per_row = 2;
        $chunks = array_chunk($this->getElements(4), $elements_per_row);

        foreach ($chunks as $index => $row_items) {
            $items = [];

            foreach ($row_items as $i => $row_item) {
                $items[] = $this->getComponentColumn([
                    $this->getComponentImage($row_item['image'], [], [
                        'margin' => '2 2 0 2',
                    ]),
                    $this->getComponentText($row_item['title'], [], [
                        'font-size' => '17',
                        'padding' => '7 0 7 0',
                    ]),
                    $this->getComponentText($row_item['description'], [], [
                        'font-size' => '13',
                        'color' => '#8b8e89',
                        'padding' => '0 0 7 0',
                        'text-align' => 'center',
                    ]),
                ], [], [
                    'width' => 100 / $elements_per_row . '%',
                    'padding' => '3 5 3 5',
                    'text-align' => 'center',
                ]);
            }

            $this->layout->scroll[] = $this->getComponentRow($items, [], [
                'width' => 'auto',
                'vertical-align' => 'middle',
                'margin' => '0 15 0 15',
            ]);

            if ( ($index+1) < count($chunks) ) {
                $this->layout->scroll[] = $this->getComponentSpacer(1, [], [
                    'background-color' => '#eeeeee',
                    'margin' => '4 0 4 0',
                ]);
            }

        }

    }

}