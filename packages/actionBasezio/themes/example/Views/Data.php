<?php

namespace packages\actionBasezio\themes\example\Views;
use packages\actionBasezio\Views\Pagetwo as BootstrapView;
use packages\actionBasezio\themes\example\Components\Components;

class Data extends BootstrapView {

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
       // $this->setTopShadow();

        $this->layout->scroll[]  = $this->getComponentText('Welcom to Basezio');

        $this->layout->scroll[] = $this->getComponentFormFieldText('', array(
            'variable' => 'title',
            'hint' => 'Tasks Title'
        ),array(
            'border-radius' => '4',
            'background-color' => '#fff',
            'padding' => '10 10 10 10',
            'margin' => '10 10 0 10'
        ));

        $this->layout->scroll[] = $this->getComponentFormFieldTextArea('', array(
            'variable' => 'Description',
            'hint' => 'Task Description'
        ), array(
            'border-radius' => '4',
            'background-color' => '#fff',
            'padding' => '10 10 10 10',
            'margin' => '10 10 0 10'
        ));


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