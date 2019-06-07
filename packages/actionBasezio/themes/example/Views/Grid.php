<?php

namespace packages\actionBasezio\themes\example\Views;
use packages\actionBasezio\Views\Pagetwo as BootstrapView;
use packages\actionBasezio\themes\example\Components\Components;

class Grid extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;
    public $tab;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1() {
        $this->layout = new \stdClass();
        $this->setTopShadow();



        $this->layout->scroll[] = $this->getComponentText('Grid View', [], [
            'padding' => '10 15 10 15',
            'font-size' => '19',
        ]);

        $this->getSimpleGrid();

//        $this->layout->overlay[] = $this->components->uiKitFloatingButtons([
//            [
//                'icon' => 'layouts-exit-icon.png',
//                'onclick' => $this->getOnclickRoute('Controller/Default', false)
//            ],
//        ], true);


        $btn1[] = $this->getComponentText('Grid',
            array('style' => 'mreg_btn1',
                'onclick' => $this->getOnclickRoute(
                    'Controller/grid/grids',
                    true,
                    array('mytestid' => 'Flower sent from Default view','exampleid' => 111),
                    true
                )));

        $btn2[] = $this->getComponentText('Setting',
            array('style' => 'mreg_btn2',
                'onclick' => $this->getOnclickRoute(
                    'Controller/setting/settings',
                    true,
                    array('mytestid' => 'Flower sent from Default view','exampleid' => 112),
                    true
                )));

        $btn3[] = $this->getComponentText('Data',
            array('style' => 'mreg_btn3',
                'onclick' => $this->getOnclickRoute(
                    'Controller/data/datas',
                    true,
                    array('mytestid' => 'Flower sent from Default view','exampleid' => 113),
                    true
                )));

        $this->layout->footer[] = $this->getComponentRow([
            $btn3,$btn1,$btn2
        ]);

        return $this->layout;
    }

    public function getSimpleGrid() {

        $elements_per_row = 3;
        $chunks = array_chunk($this->getElements(6), $elements_per_row);

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

    public function getElements( $count = 6 ) {

        $elements = [
            [
                'title' => 'Element 1',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo5.png'
            ],
            [
                'title' => 'Element 2',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo6.jpg'
            ],
            [
                'title' => 'Element 3',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo7.png'
            ],
            [
                'title' => 'Element 4',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo8.jpg'
            ],
            [
                'title' => 'Element 5',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo9.png'
            ],
            [
                'title' => 'Element 6',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'logo10.png'
            ],
        ];

        return array_slice($elements, 0, $count);
    }


}