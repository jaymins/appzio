<?php

namespace packages\actionBasezio\themes\example\Views;
use packages\actionBasezio\Views\Pagetwo as BootstrapView;
use packages\actionBasezio\themes\example\Components\Components;

class Setting extends BootstrapView {

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



        $this->layout->scroll[] = $this->getComponentText('Settings', [], [
            'padding' => '10 15 10 15',
            'font-size' => '22',
            'font-weight' => 'bold',
        ]);

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentColumn([
                $this->getComponentRow([
                    $this->getComponentImage('logo9.png', [
                    ], [
                        'crop' => 'round',
                        'width' => '30',
                        'height' => '30',
                        'vertical-align' => 'middle'
                    ]),
                    $this->getComponentText(strtoupper('Profile Name'), [], [
                        'color' => '#000',
                        'font-size' => '19',
                        'padding' => '15 15 15 15',
                       // 'vertical-align' => 'middle',
                    ])
                ], [], [
                    'width' => 'auto',
                    'margin' => '0 15 0 15',
                    'vertical-align' => 'bottom',
                ])
            ], [], [
                'width' => 'auto',
                'height' => $this->screen_height / 3,
                'background-image' => $this->getImageFileName('bg1.jpg'),
                'background-size' => 'cover',
            ])
        ], [], [
            'width' => 'auto',
            'height' => $this->screen_height / 3,
            'background-image' => $this->getImageFileName('logo7.jpg'),
            'background-size' => 'cover',
        ]);

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




}